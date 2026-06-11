<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Notifications\Notification;

class WebPushChannel
{
    public function __construct(private WebPushService $webPush) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable instanceof User || ! $this->webPush->isConfigured()) {
            return;
        }

        if (! $notifiable->pushSubscriptions()->exists()) {
            return;
        }

        if (! method_exists($notification, 'toArray')) {
            return;
        }

        $payload = $this->webPush->formatPayload($notification->toArray($notifiable));

        $this->webPush->sendToUser(
            $notifiable,
            $payload['title'],
            $payload['body'],
            $payload['url'],
        );
    }
}
