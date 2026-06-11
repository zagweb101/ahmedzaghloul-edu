<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ProcessSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:process';

    protected $description = 'Expire due subscriptions and notify users before expiry';

    public function handle(SubscriptionService $subscriptions): int
    {
        $expiring = $subscriptions->notifyExpiringSubscriptions();
        $expired = $subscriptions->expireDueSubscriptions();

        $this->info("Notified {$expiring} expiring subscription(s).");
        $this->info("Expired {$expired} subscription(s).");

        return self::SUCCESS;
    }
}
