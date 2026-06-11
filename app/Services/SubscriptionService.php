<?php

namespace App\Services;

use App\Enums\AccessLevel;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionActivatedNotification;

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
