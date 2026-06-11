<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionCheckoutController extends Controller
{
    public function show(SubscriptionPlan $subscriptionPlan): View
    {
        abort_unless($subscriptionPlan->is_active, 404);

        return view('subscription-plans.checkout', [
            'plan' => $subscriptionPlan,
        ]);
    }

    public function store(Request $request, SubscriptionPlan $subscriptionPlan, PaymentService $payments): RedirectResponse
    {
        abort_unless($subscriptionPlan->is_active, 404);

        if ($subscriptionPlan->isFree()) {
            $payments->activateFreePlan($request->user());

            return redirect()
                ->route('dashboard')
                ->with('status', 'تم تفعيل الخطة المجانية بنجاح.');
        }

        $validated = $request->validate([
            'customer_note' => ['nullable', 'string', 'max:500'],
        ]);

        $order = $payments->checkout(
            $request->user(),
            $subscriptionPlan,
            $validated['customer_note'] ?? null,
        );

        if ($order->checkout_url && $order->isPending()) {
            return redirect()->away($order->checkout_url);
        }

        if ($order->isPaid()) {
            return redirect()
                ->route('subscription-orders.show', $order)
                ->with('status', 'تم تفعيل اشتراكك بنجاح.');
        }

        return redirect()
            ->route('subscription-orders.show', $order)
            ->with('status', 'تم إنشاء طلب الاشتراك. أكمل التحويل لتفعيل حسابك.');
    }

    public function showOrder(SubscriptionOrder $subscriptionOrder): View
    {
        abort_unless($subscriptionOrder->user_id === auth()->id(), 403);

        return view('subscription-plans.order', [
            'order' => $subscriptionOrder->load('plan'),
        ]);
    }
}
