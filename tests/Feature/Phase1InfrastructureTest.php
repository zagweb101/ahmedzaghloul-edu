<?php

namespace Tests\Feature;

use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase1InfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'platform.backup_disk' => 'backups',
            'platform.backup_path' => '',
            'platform.backup_retention_days' => 14,
        ]);

        if (! is_link(public_path('storage')) && ! is_dir(public_path('storage'))) {
            Artisan::call('storage:link');
        }
    }

    public function test_platform_health_check_passes_after_setup(): void
    {
        $this->seed(PlatformSeeder::class);

        $this->artisan('platform:health-check')
            ->assertSuccessful()
            ->expectsOutputToContain('All production health checks passed.');
    }

    public function test_platform_backup_creates_storage_archive(): void
    {
        Storage::disk('local')->put('public/phase1-backup-check.txt', 'backup-test');

        $this->artisan('platform:backup', ['--storage' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('Storage backup created.');

        $files = Storage::disk('backups')->files();
        $this->assertNotEmpty($files);
        $this->assertTrue(
            collect($files)->contains(fn ($file) => str_contains($file, 'storage_') && str_ends_with($file, '.zip')),
        );
    }

    public function test_platform_backup_creates_sqlite_database_archive(): void
    {
        $databaseFile = storage_path('app/phase1-database.sqlite');

        if (file_exists($databaseFile)) {
            unlink($databaseFile);
        }

        touch($databaseFile);

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $databaseFile,
        ]);

        $this->artisan('migrate', ['--force' => true]);

        $this->artisan('platform:backup', ['--database' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('Database backup created.');

        $files = Storage::disk('backups')->files();
        $this->assertTrue(
            collect($files)->contains(fn ($file) => str_contains($file, 'database_') && str_ends_with($file, '.zip')),
        );

        if (file_exists($databaseFile)) {
            unlink($databaseFile);
        }
    }

    public function test_platform_backup_prunes_old_files(): void
    {
        Storage::disk('backups')->put('old-backup.zip', 'old');
        touch(
            Storage::disk('backups')->path('old-backup.zip'),
            now()->subDays(30)->getTimestamp(),
        );

        Storage::disk('local')->put('public/prune-check.txt', 'data');

        $this->artisan('platform:backup', ['--storage' => true])->assertSuccessful();

        $this->assertFalse(Storage::disk('backups')->exists('old-backup.zip'));
    }

    public function test_platform_log_review_runs_successfully(): void
    {
        File::ensureDirectoryExists(storage_path('logs'));
        file_put_contents(storage_path('logs/laravel.log'), "[2026-06-11 10:00:00] production.ERROR: test error\n");

        $this->artisan('platform:log-review')
            ->assertSuccessful()
            ->expectsOutputToContain('Log review for the last');
    }

    public function test_infrastructure_tasks_are_scheduled(): void
    {
        $this->artisan('schedule:list')
            ->assertSuccessful()
            ->expectsOutputToContain('platform:backup')
            ->expectsOutputToContain('platform:log-review')
            ->expectsOutputToContain('platform:health-check')
            ->expectsOutputToContain('live-events:send-reminders');
    }

    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }
}
