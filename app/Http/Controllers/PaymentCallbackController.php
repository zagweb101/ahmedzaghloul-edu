<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentGatewayFactory;
use App\Services\Payments\StripePaymentGateway;
use App\Services\Payments\TapPaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function tapWebhook(Request $request, PaymentGatewayFactory $gateways): JsonResponse
    {
        $gateway = $gateways->make('tap');

        if (! $gateway instanceof TapPaymentGateway) {
            abort(404);
        }

        $order = $gateway->handleWebhook($request->all());

        return response()->json([
            'received' => true,
            'order_id' => $order?->id,
            'status' => $order?->status,
        ]);
    }

    public function stripeWebhook(Request $request, PaymentGatewayFactory $gateways): JsonResponse
    {
        $gateway = $gateways->make('stripe');

        if (! $gateway instanceof StripePaymentGateway) {
            abort(404);
        }

        $payload = json_decode($request->getContent(), true) ?? [];

        $order = $gateway->handleWebhook(
            $payload,
            $request->header('Stripe-Signature'),
            $request->getContent(),
        );

        return response()->json([
            'received' => true,
            'order_id' => $order?->id,
            'status' => $order?->status,
        ]);
    }

    public function tapReturn(Request $request, PaymentGatewayFactory $gateways): RedirectResponse
    {
        $gateway = $gateways->make('tap');

        if (! $gateway instanceof TapPaymentGateway) {
            return redirect()->route('subscription-plans.index');
        }

        $order = $gateway->verifyReturn($request->query('tap_id'));

        if (! $order) {
            return redirect()
                ->route('subscription-plans.index')
                ->with('status', 'تعذر العثور على طلب الدفع.');
        }

        $message = $order->isPaid()
            ? 'تم تفعيل اشتراكك بنجاح.'
            : 'طلبك قيد المعالجة. سنُعلمك عند اكتمال الدفع.';

        return redirect()
            ->route('subscription-orders.show', $order)
            ->with('status', $message);
    }

    public function stripeReturn(Request $request, PaymentGatewayFactory $gateways): RedirectResponse
    {
        $gateway = $gateways->make('stripe');

        if (! $gateway instanceof StripePaymentGateway) {
            return redirect()->route('subscription-plans.index');
        }

        $order = $gateway->verifyReturn($request->query('session_id'));

        if (! $order) {
            return redirect()
                ->route('subscription-plans.index')
                ->with('status', 'تعذر العثور على طلب الدفع.');
        }

        $message = $order->isPaid()
            ? 'تم تفعيل اشتراكك بنجاح.'
            : 'طلبك قيد المعالجة. سنُعلمك عند اكتمال الدفع.';

        return redirect()
            ->route('subscription-orders.show', $order)
            ->with('status', $message);
    }
}
