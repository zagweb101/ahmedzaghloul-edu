<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Notifications
    |--------------------------------------------------------------------------
    |
    | When enabled, platform notifications are also sent by email in addition
    | to the in-app notifications center.
    |
    */

    'mail_notifications' => (bool) env('NOTIFY_VIA_MAIL', false),

    /*
    |--------------------------------------------------------------------------
    | Live Event Reminders
    |--------------------------------------------------------------------------
    |
    | Hours before a live event starts to send reminder notifications.
    |
    */

    'live_event_reminder_hours' => (int) env('LIVE_EVENT_REMINDER_HOURS', 24),

];
