<?php

namespace App\Services;

use App\Enums\AccessLevel;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private SubscriptionService $subscriptions,
        private PaymentGatewayFactory $gateways,
    ) {}

    public function checkout(User $user, SubscriptionPlan $plan, ?string $customerNote = null): SubscriptionOrder
    {
        abort_if($plan->slug === 'free', 422, 'الخطة المجانية لا تحتاج دفعًا.');

        $existingPending = SubscriptionOrder::query()
            ->where('user_id', $user->id)
            ->where('subscription_plan_id', $plan->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return $existingPending;
        }

        $driver = config('payments.driver', 'manual');

        $order = DB::transaction(function () use ($user, $plan, $customerNote, $driver) {
            return SubscriptionOrder::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'reference' => SubscriptionOrder::generateReference(),
                'amount_cents' => $plan->price_cents,
                'currency' => $plan->currency,
                'status' => 'pending',
                'payment_driver' => $driver,
                'customer_note' => $customerNote,
            ]);
        });

        if ($driver === 'demo') {
            $this->markPaid($order);

            return $order->fresh();
        }

        return $this->gateways->make($driver)->initiate($order)->order;
    }

    public function activateFreePlan(User $user): void
    {
        $plan = SubscriptionPlan::where('slug', 'free')->firstOrFail();
        $this->subscriptions->activate($user, $plan, AccessLevel::Free);
    }

    public function markPaid(SubscriptionOrder $order): UserSubscription
    {
        abort_unless($order->isPending(), 422);

        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $this->subscriptions->activate(
                $order->user()->firstOrFail(),
                $order->plan()->firstOrFail(),
            );
        });
    }

    public function cancel(SubscriptionOrder $order): void
    {
        abort_unless($order->isPending(), 422);

        $order->update(['status' => 'cancelled']);
    }
}
