<?php

namespace App\Notifications\Concerns;

trait UsesPlatformChannels
{
    /** @return list<string> */
    protected function platformChannels(): array
    {
        $channels = ['database'];

        if (config('platform.mail_notifications')) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
