<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PlatformHealthCheckCommand extends Command
{
    protected $signature = 'platform:health-check';

    protected $description = 'Verify production readiness: database, storage, cache, and app key';

    public function handle(): int
    {
        $checks = [
            'APP_KEY configured' => fn () => (bool) config('app.key'),
            'Database connection' => fn () => DB::connection()->getPdo() !== null,
            'Migrations table exists' => fn () => Schema::hasTable('migrations'),
            'Storage directory writable' => fn () => is_writable(storage_path()),
            'Bootstrap cache writable' => fn () => is_writable(base_path('bootstrap/cache')),
            'Public storage linked' => fn () => is_link(public_path('storage')) || is_dir(public_path('storage')),
        ];

        $failed = 0;

        foreach ($checks as $label => $check) {
            try {
                $passed = $check();

                if ($passed) {
                    $this->line("<info>OK</info> {$label}");
                } else {
                    $this->line("<error>FAIL</error> {$label}");
                    $failed++;
                }
            } catch (\Throwable $exception) {
                $this->line("<error>FAIL</error> {$label} ({$exception->getMessage()})");
                $failed++;
            }
        }

        if (app()->environment('production') && config('app.debug')) {
            $this->warn('APP_DEBUG is enabled. Disable it on production.');
            $failed++;
        }

        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile) && File::size($logFile) > 50 * 1024 * 1024) {
            $this->warn('laravel.log is larger than 50MB. Consider pruning or switching to daily logs.');
        }

        if ($failed > 0) {
            $this->error("Health check failed ({$failed} issue(s)).");

            return self::FAILURE;
        }

        $this->info('All production health checks passed.');

        return self::SUCCESS;
    }
}
