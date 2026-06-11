<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('live-events:send-reminders')->hourly();
Schedule::command('subscriptions:process')->dailyAt('08:00');
Schedule::command('platform:backup')->dailyAt('02:00');
Schedule::command('platform:log-review')->dailyAt('06:00');
Schedule::command('platform:health-check')->weeklyOn(1, '07:00');
