<?php

return [

    'enabled' => (bool) env('PUSH_NOTIFICATIONS_ENABLED', false),

    'vapid' => [
        'subject' => env('PUSH_VAPID_SUBJECT', env('APP_URL', 'mailto:admin@example.com')),
        'public_key' => env('PUSH_VAPID_PUBLIC_KEY'),
        'private_key' => env('PUSH_VAPID_PRIVATE_KEY'),
    ],

];
