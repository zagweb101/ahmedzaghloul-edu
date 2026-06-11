<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PlatformLogReviewCommand extends Command
{
    protected $signature = 'platform:log-review';

    protected $description = 'Summarize recent application errors from log files';

    public function handle(): int
    {
        $days = config('monitoring.log_review_days', 7);
        $cutoff = now()->subDays($days)->getTimestamp();
        $summary = [
            'error' => 0,
            'critical' => 0,
            'alert' => 0,
            'emergency' => 0,
        ];

        foreach (File::glob(storage_path('logs/*.log')) as $logFile) {
            if (File::lastModified($logFile) < $cutoff) {
                continue;
            }

            $handle = fopen($logFile, 'r');

            if (! $handle) {
                continue;
            }

            while (($line = fgets($handle)) !== false) {
                foreach (array_keys($summary) as $level) {
                    if (stripos($line, ".{$level}:") !== false || stripos($line, strtoupper($level)) !== false) {
                        $summary[$level]++;
                    }
                }
            }

            fclose($handle);
        }

        $total = array_sum($summary);

        $this->info("Log review for the last {$days} day(s)");

        foreach ($summary as $level => $count) {
            $this->line(sprintf(' - %s: %d', strtoupper($level), $count));
        }

        if ($total === 0) {
            $this->comment('No elevated log levels detected in recent files.');
        } elseif ($total >= 10) {
            $this->warn('Elevated error volume detected. Review storage/logs and consider Sentry alerting.');
        }

        $alertEmail = config('monitoring.alert_email');

        if ($alertEmail && $total > 0) {
            $this->comment("Alert email configured for [{$alertEmail}] — wire MAIL_* to enable notifications.");
        }

        return self::SUCCESS;
    }
}
