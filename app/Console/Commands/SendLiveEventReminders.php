<?php

namespace App\Console\Commands;

use App\Models\LiveEventRegistration;
use App\Notifications\LiveEventReminderNotification;
use App\Notifications\LiveEventStartedNotification;
use Illuminate\Console\Command;

class SendLiveEventReminders extends Command
{
    protected $signature = 'live-events:send-reminders';

    protected $description = 'Send reminder notifications for upcoming live events';

    public function handle(): int
    {
        $hours = config('platform.live_event_reminder_hours', 24);
        $windowStart = now();
        $windowEnd = now()->addHours($hours);

        $registrations = LiveEventRegistration::query()
            ->with(['user', 'event'])
            ->where('status', 'registered')
            ->whereNull('reminder_sent_at')
            ->whereHas('event', function ($query) use ($windowStart, $windowEnd) {
                $query
                    ->where('is_published', true)
                    ->whereNotNull('starts_at')
                    ->whereBetween('starts_at', [$windowStart, $windowEnd]);
            })
            ->get();

        $sent = 0;

        foreach ($registrations as $registration) {
            if (! $registration->user || ! $registration->event) {
                continue;
            }

            $registration->user->notify(new LiveEventReminderNotification($registration->event));
            $registration->update(['reminder_sent_at' => now()]);
            $sent++;
        }

        $this->info("Sent {$sent} live event reminder(s).");

        $started = 0;

        $startedRegistrations = LiveEventRegistration::query()
            ->with(['user', 'event'])
            ->where('status', 'registered')
            ->whereNull('started_notified_at')
            ->whereHas('event', function ($query) {
                $query
                    ->where('is_published', true)
                    ->whereNotNull('starts_at')
                    ->where('starts_at', '<=', now())
                    ->where('starts_at', '>=', now()->subHour());
            })
            ->get();

        foreach ($startedRegistrations as $registration) {
            if (! $registration->user || ! $registration->event) {
                continue;
            }

            $registration->user->notify(new LiveEventStartedNotification($registration->event));
            $registration->update(['started_notified_at' => now()]);
            $started++;
        }

        if ($started > 0) {
            $this->info("Sent {$started} live started notification(s).");
        }

        return self::SUCCESS;
    }
}

