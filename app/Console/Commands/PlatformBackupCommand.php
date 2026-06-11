<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class PlatformBackupCommand extends Command
{
    protected $signature = 'platform:backup
                            {--database : Backup database only}
                            {--storage : Backup uploaded files only}';

    protected $description = 'Create database and/or storage backups';

    public function handle(BackupService $backups): int
    {
        $databaseOnly = (bool) $this->option('database');
        $storageOnly = (bool) $this->option('storage');
        $runAll = ! $databaseOnly && ! $storageOnly;

        $created = [];

        if ($runAll || $databaseOnly) {
            try {
                $created[] = $backups->backupDatabase();
                $this->info('Database backup created.');
            } catch (\Throwable $exception) {
                $this->error('Database backup failed: ' . $exception->getMessage());
            }
        }

        if ($runAll || $storageOnly) {
            try {
                $created[] = $backups->backupStorage();
                $this->info('Storage backup created.');
            } catch (\Throwable $exception) {
                $this->error('Storage backup failed: ' . $exception->getMessage());
            }
        }

        $deleted = $backups->pruneOldBackups();

        foreach ($created as $path) {
            $this->line(" - {$path}");
        }

        if ($deleted > 0) {
            $this->comment("Pruned {$deleted} old backup file(s).");
        }

        return empty($created) ? self::FAILURE : self::SUCCESS;
    }
}
