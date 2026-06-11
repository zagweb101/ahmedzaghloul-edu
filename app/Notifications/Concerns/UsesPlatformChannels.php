<?php

namespace App\Notifications\Concerns;

use App\Models\User;

trait UsesPlatformChannels
{
    /** @return list<string> */
    protected function platformChannels(object $notifiable): array
    {
        $channels = ['database'];

        if (config('platform.mail_notifications')) {
            $channels[] = 'mail';
        }

        if (
            $notifiable instanceof User
            && config('push.enabled')
            && $notifiable->pushSubscriptions()->exists()
        ) {
            $channels[] = 'webpush';
        }

        return $channels;
    }
}
