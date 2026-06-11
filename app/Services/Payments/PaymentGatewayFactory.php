<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public function make(?string $driver = null): PaymentGateway
    {
        $driver ??= config('payments.driver', 'manual');

        return match ($driver) {
            'manual' => new ManualPaymentGateway,
            'tap' => new TapPaymentGateway,
            'stripe' => new StripePaymentGateway,
            default => throw new InvalidArgumentException("بوابة الدفع [{$driver}] غير مدعومة."),
        };
    }
}
