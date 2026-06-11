<?php

namespace App\Services\Payments;

use App\Models\SubscriptionOrder;

class PaymentInitiationResult
{
    public function __construct(
        public SubscriptionOrder $order,
        public ?string $redirectUrl = null,
    ) {}
}
