<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class BackupService
{
    public function backupDatabase(): string
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        return match ($connection) {
            'mysql', 'mariadb' => $this->backupMysqlDatabase($config),
            'sqlite' => $this->backupSqliteDatabase($config),
            default => throw new RuntimeException("Database driver [{$connection}] is not supported for backups."),
        };
    }

    public function backupStorage(): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $zipName = "storage_{$timestamp}.zip";
        $zipPath = $this->temporaryPath($zipName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create storage backup archive.');
        }

        foreach (['public', 'private'] as $directory) {
            $absolute = storage_path("app/{$directory}");

            if (! is_dir($absolute)) {
                continue;
            }

            $this->addDirectoryToZip($zip, $absolute, $directory);
        }

        $zip->close();

        return $this->storeArchive($zipPath, $zipName);
    }

    public function pruneOldBackups(): int
    {
        $disk = Storage::disk(config('platform.backup_disk', 'backups'));
        $path = trim((string) config('platform.backup_path', ''), '/');
        $retentionDays = config('platform.backup_retention_days', 14);
        $cutoff = now()->subDays($retentionDays)->getTimestamp();
        $deleted = 0;

        foreach ($disk->files($path) as $file) {
            if ($disk->lastModified($file) < $cutoff) {
                $disk->delete($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function backupMysqlDatabase(array $config): string
    {
        $timestamp = now()->format('Y-m-d_His');
        $sqlName = "database_{$timestamp}.sql";
        $sqlPath = $this->temporaryPath($sqlName);

        $host = $config['host'] ?? '127.0.0.1';

        if (in_array($host, ['localhost', '::1'], true)) {
            $host = '127.0.0.1';
        }

        $command = [
            'mysqldump',
            '--host=' . $host,
            '--port=' . ($config['port'] ?? '3306'),
            '--user=' . ($config['username'] ?? 'root'),
            '--single-transaction',
            '--quick',
            '--result-file=' . $sqlPath,
            $config['database'] ?? '',
        ];

        $password = $config['password'] ?? null;

        $result = Process::env($password ? ['MYSQL_PWD' => $password] : [])
            ->run($command);

        if (! $result->successful() || ! file_exists($sqlPath)) {
            throw new RuntimeException(trim($result->errorOutput() ?: 'MySQL backup failed.'));
        }

        $zipName = "database_{$timestamp}.zip";
        $zipPath = $this->temporaryPath($zipName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not compress database backup.');
        }

        $zip->addFile($sqlPath, $sqlName);
        $zip->close();

        File::delete($sqlPath);

        return $this->storeArchive($zipPath, $zipName);
    }

    private function backupSqliteDatabase(array $config): string
    {
        $database = $config['database'] ?? null;

        if (! $database || $database === ':memory:') {
            throw new RuntimeException('SQLite in-memory databases cannot be backed up to disk.');
        }

        if (! file_exists($database)) {
            throw new RuntimeException("SQLite database file not found at [{$database}].");
        }

        $timestamp = now()->format('Y-m-d_His');
        $zipName = "database_{$timestamp}.zip";
        $zipPath = $this->temporaryPath($zipName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not compress SQLite backup.');
        }

        $zip->addFile($database, 'database.sqlite');
        $zip->close();

        return $this->storeArchive($zipPath, $zipName);
    }

    private function storeArchive(string $sourcePath, string $destinationName): string
    {
        $disk = Storage::disk(config('platform.backup_disk', 'backups'));
        $path = trim((string) config('platform.backup_path', ''), '/');
        $destination = $path !== '' ? "{$path}/{$destinationName}" : $destinationName;

        $disk->put($destination, file_get_contents($sourcePath));
        File::delete($sourcePath);

        return $destination;
    }

    private function temporaryPath(string $filename): string
    {
        $directory = storage_path('app/backup-temp');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return $directory . DIRECTORY_SEPARATOR . $filename;
    }

    private function addDirectoryToZip(ZipArchive $zip, string $directory, string $zipRoot): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $relative = $zipRoot . '/' . ltrim(str_replace($directory, '', $file->getPathname()), DIRECTORY_SEPARATOR . '/');

            if ($file->isDir()) {
                $zip->addEmptyDir(rtrim($relative, '/'));

                continue;
            }

            $zip->addFile($file->getPathname(), $relative);
        }
    }
}
