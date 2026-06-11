<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\SubscriptionOrder;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripePaymentGateway implements PaymentGateway
{
    public function driver(): string
    {
        return 'stripe';
    }

    public function initiate(SubscriptionOrder $order): PaymentInitiationResult
    {
        $secretKey = config('payments.stripe.secret_key');

        if (! $secretKey) {
            Log::warning('Stripe: STRIPE_SECRET_KEY غير مضبوط، الطلب سيبقى معلقًا.');

            return new PaymentInitiationResult($order);
        }

        $order->loadMissing('user', 'plan');

        $response = Http::withToken($secretKey)
            ->asForm()
            ->post(config('payments.stripe.api_url') . '/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => route('payments.stripe.return') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription-plans.checkout', $order->plan),
                'client_reference_id' => $order->reference,
                'customer_email' => $order->user->email,
                'metadata[order_id]' => (string) $order->id,
                'metadata[reference]' => $order->reference,
                'line_items[0][price_data][currency]' => strtolower($order->currency),
                'line_items[0][price_data][product_data][name]' => 'اشتراك ' . ($order->plan?->name ?? 'بيت المصور'),
                'line_items[0][price_data][unit_amount]' => $order->amount_cents,
                'line_items[0][quantity]' => 1,
            ]);

        if (! $response->successful()) {
            Log::error('Stripe: فشل إنشاء جلسة الدفع.', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return new PaymentInitiationResult($order);
        }

        $sessionId = $response->json('id');
        $checkoutUrl = $response->json('url');

        $order->update([
            'gateway_charge_id' => $sessionId,
            'checkout_url' => $checkoutUrl,
        ]);

        return new PaymentInitiationResult($order->fresh(), $checkoutUrl);
    }

    public function handleWebhook(array $payload, ?string $signature = null, ?string $rawPayload = null): ?SubscriptionOrder
    {
        if (! $this->verifyWebhookSignature($rawPayload ?? '', $signature)) {
            Log::warning('Stripe: webhook signature verification failed.');

            return null;
        }

        $eventType = data_get($payload, 'type');

        if ($eventType !== 'checkout.session.completed') {
            return null;
        }

        $session = data_get($payload, 'data.object', []);
        $order = $this->resolveOrder($session);

        if (! $order || ! $order->isPending()) {
            return $order;
        }

        if ($this->isPaid($session)) {
            app(PaymentService::class)->markPaid($order);
        }

        return $order->fresh();
    }

    public function verifyReturn(?string $sessionId): ?SubscriptionOrder
    {
        if (! $sessionId) {
            return null;
        }

        $secretKey = config('payments.stripe.secret_key');

        if (! $secretKey) {
            return SubscriptionOrder::where('gateway_charge_id', $sessionId)->first();
        }

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->get(config('payments.stripe.api_url') . '/checkout/sessions/' . $sessionId);

        if (! $response->successful()) {
            return SubscriptionOrder::where('gateway_charge_id', $sessionId)->first();
        }

        $session = $response->json() ?? [];
        $order = $this->resolveOrder($session);

        if ($order && $order->isPending() && $this->isPaid($session)) {
            app(PaymentService::class)->markPaid($order);
        }

        return $order?->fresh();
    }

    private function resolveOrder(array $session): ?SubscriptionOrder
    {
        $orderId = data_get($session, 'metadata.order_id');

        if ($orderId) {
            return SubscriptionOrder::find($orderId);
        }

        $reference = data_get($session, 'client_reference_id');

        if ($reference) {
            return SubscriptionOrder::where('reference', $reference)->first();
        }

        $sessionId = data_get($session, 'id');

        if ($sessionId) {
            return SubscriptionOrder::where('gateway_charge_id', $sessionId)->first();
        }

        return null;
    }

    private function isPaid(array $session): bool
    {
        return data_get($session, 'payment_status') === 'paid';
    }

    private function verifyWebhookSignature(string $rawPayload, ?string $signature): bool
    {
        $secret = config('payments.stripe.webhook_secret');

        if (! $secret) {
            return app()->environment('testing');
        }

        if (! $signature || $rawPayload === '') {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signature) as $part) {
            [$key, $value] = array_map('trim', explode('=', $part, 2) + [null, null]);

            if ($key === 't') {
                $timestamp = $value;
            }

            if ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if (! $timestamp || $signatures === []) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $rawPayload;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        foreach ($signatures as $value) {
            if (hash_equals($expected, $value)) {
                return true;
            }
        }

        return false;
    }
}
