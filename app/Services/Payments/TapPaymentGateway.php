<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\SubscriptionOrder;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TapPaymentGateway implements PaymentGateway
{

    public function driver(): string
    {
        return 'tap';
    }

    public function initiate(SubscriptionOrder $order): PaymentInitiationResult
    {
        $secretKey = config('payments.tap.secret_key');

        if (! $secretKey) {
            Log::warning('Tap Payments: TAP_SECRET_KEY غير مضبوط، الطلب سيبقى معلقًا.');

            return new PaymentInitiationResult($order);
        }

        $order->loadMissing('user', 'plan');

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->post(config('payments.tap.api_url') . '/charges', [
                'amount' => round($order->amount_cents / 100, 2),
                'currency' => $order->currency,
                'threeDSecure' => true,
                'save_card' => false,
                'description' => 'اشتراك ' . ($order->plan?->name ?? 'بيت المصور'),
                'customer' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'source' => [
                    'id' => config('payments.tap.source_id', 'src_all'),
                ],
                'redirect' => [
                    'url' => route('payments.tap.return'),
                ],
                'post' => [
                    'url' => route('payments.tap.webhook'),
                ],
                'reference' => [
                    'transaction' => $order->reference,
                    'order' => $order->reference,
                ],
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'reference' => $order->reference,
                ],
            ]);

        if (! $response->successful()) {
            Log::error('Tap Payments: فشل إنشاء عملية الدفع.', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return new PaymentInitiationResult($order);
        }

        $chargeId = $response->json('id');
        $checkoutUrl = $response->json('transaction.url');

        $order->update([
            'gateway_charge_id' => $chargeId,
            'checkout_url' => $checkoutUrl,
        ]);

        return new PaymentInitiationResult($order->fresh(), $checkoutUrl);
    }

    public function handleWebhook(array $payload, ?string $signature = null, ?string $rawPayload = null): ?SubscriptionOrder
    {
        $order = $this->resolveOrder($payload);

        if (! $order || ! $order->isPending()) {
            return $order;
        }

        if ($this->isCaptured($payload)) {
            app(PaymentService::class)->markPaid($order);
        }

        return $order->fresh();
    }

    public function verifyReturn(?string $chargeId): ?SubscriptionOrder
    {
        if (! $chargeId) {
            return null;
        }

        $secretKey = config('payments.tap.secret_key');

        if (! $secretKey) {
            return SubscriptionOrder::where('gateway_charge_id', $chargeId)->first();
        }

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->get(config('payments.tap.api_url') . '/charges/' . $chargeId);

        if (! $response->successful()) {
            return SubscriptionOrder::where('gateway_charge_id', $chargeId)->first();
        }

        $order = $this->resolveOrder($response->json() ?? []);

        if ($order && $order->isPending() && $this->isCaptured($response->json() ?? [])) {
            app(PaymentService::class)->markPaid($order);
        }

        return $order?->fresh();
    }

    private function resolveOrder(array $payload): ?SubscriptionOrder
    {
        $orderId = data_get($payload, 'metadata.order_id');
        $reference = data_get($payload, 'reference.order')
            ?? data_get($payload, 'reference.transaction');

        if ($orderId) {
            return SubscriptionOrder::find($orderId);
        }

        if ($reference) {
            return SubscriptionOrder::where('reference', $reference)->first();
        }

        $chargeId = data_get($payload, 'id');

        if ($chargeId) {
            return SubscriptionOrder::where('gateway_charge_id', $chargeId)->first();
        }

        return null;
    }

    private function isCaptured(array $payload): bool
    {
        $status = strtoupper((string) data_get($payload, 'status', ''));

        return in_array($status, ['CAPTURED', 'PAID'], true);
    }
}
