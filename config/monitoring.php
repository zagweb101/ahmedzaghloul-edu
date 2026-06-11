<?php

return [

    'sentry_dsn' => env('SENTRY_LARAVEL_DSN'),

    'alert_email' => env('MONITORING_ALERT_EMAIL'),

    'log_review_days' => (int) env('MONITORING_LOG_REVIEW_DAYS', 7),

    'health_check_timeout_seconds' => (int) env('MONITORING_HEALTH_TIMEOUT', 5),

];
