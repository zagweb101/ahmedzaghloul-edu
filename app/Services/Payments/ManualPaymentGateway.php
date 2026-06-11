<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Models\SubscriptionOrder;

class ManualPaymentGateway implements PaymentGateway
{
    public function driver(): string
    {
        return 'manual';
    }

    public function initiate(SubscriptionOrder $order): PaymentInitiationResult
    {
        return new PaymentInitiationResult($order);
    }

    public function handleWebhook(array $payload): ?SubscriptionOrder
    {
        return null;
    }
}
