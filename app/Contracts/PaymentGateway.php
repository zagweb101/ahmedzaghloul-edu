<?php

namespace App\Contracts;

use App\Models\SubscriptionOrder;
use App\Services\Payments\PaymentInitiationResult;

interface PaymentGateway
{
    public function driver(): string;

    public function initiate(SubscriptionOrder $order): PaymentInitiationResult;

    public function handleWebhook(array $payload): ?SubscriptionOrder;
}
