<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GeneratePushVapidKeysCommand extends Command
{
    protected $signature = 'push:generate-vapid';

    protected $description = 'Generate VAPID keys for browser push notifications';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->info('Add these lines to your .env file:');
        $this->line('PUSH_NOTIFICATIONS_ENABLED=true');
        $this->line('PUSH_VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('PUSH_VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('PUSH_VAPID_SUBJECT=' . config('app.url'));

        return self::SUCCESS;
    }
}
