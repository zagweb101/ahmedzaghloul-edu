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

    'subscription_expiring_days' => (int) env('SUBSCRIPTION_EXPIRING_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Automated Backups
    |--------------------------------------------------------------------------
    */

    'backup_disk' => env('BACKUP_DISK', 'backups'),

    'backup_path' => env('BACKUP_PATH', ''),

    'backup_retention_days' => (int) env('BACKUP_RETENTION_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Seed Accounts (remove before commercial launch)
    |--------------------------------------------------------------------------
    */

    'seed_user_emails' => [
        'admin@example.com',
        'test@example.com',
    ],

];
