<?php

namespace App\Services;

use App\Enums\AccessLevel;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionActivatedNotification;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;

class SubscriptionService
{
    public function activate(
        User $user,
        SubscriptionPlan $plan,
        AccessLevel|string|null $accessLevel = null,
    ): UserSubscription {
        $resolvedAccess = $accessLevel instanceof AccessLevel
            ? $accessLevel
            : ($accessLevel ? AccessLevel::from($accessLevel) : $plan->defaultAccessLevel());

        $user->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $subscription = $user->subscriptions()->create([
            'subscription_plan_id' => $plan->id,
            'access_level' => $resolvedAccess,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $this->resolveEndsAt($plan),
        ]);

        $user->notify(new SubscriptionActivatedNotification($plan));

        return $subscription;
    }

    public function expireDueSubscriptions(): int
    {
        $expired = 0;

        UserSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->with(['user', 'plan'])
            ->get()
            ->each(function (UserSubscription $subscription) use (&$expired) {
                $subscription->update(['status' => 'expired']);

                if ($subscription->user && $subscription->plan) {
                    $subscription->user->notify(new SubscriptionExpiredNotification($subscription->plan));
                }

                $expired++;
            });

        return $expired;
    }

    public function notifyExpiringSubscriptions(): int
    {
        $days = config('platform.subscription_expiring_days', 7);
        $notified = 0;

        UserSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereNull('expiring_notified_at')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', now()->addDays($days))
            ->with(['user', 'plan'])
            ->get()
            ->each(function (UserSubscription $subscription) use (&$notified) {
                if (! $subscription->user || ! $subscription->plan) {
                    return;
                }

                $subscription->user->notify(new SubscriptionExpiringNotification($subscription->plan));
                $subscription->update(['expiring_notified_at' => now()]);
                $notified++;
            });

        return $notified;
    }

    private function resolveEndsAt(SubscriptionPlan $plan): ?\Illuminate\Support\Carbon
    {
        return match ($plan->billing_period) {
            'year' => now()->addYear(),
            'lifetime' => null,
            'free' => null,
            default => now()->addMonth(),
        };
    }
}
