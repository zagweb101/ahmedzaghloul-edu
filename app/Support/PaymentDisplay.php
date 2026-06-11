<?php

namespace App\Support;

class PaymentDisplay
{
    /**
     * @return array{bank_name: string, account_name: string, iban: string, instructions: string}
     */
    public static function bankDetails(): array
    {
        if (config('payments.demo_mode')) {
            return config('payments.demo_bank');
        }

        return config('payments.manual');
    }

    public static function isStripeTestMode(): bool
    {
        $key = (string) config('payments.stripe.secret_key', '');

        return str_starts_with($key, 'sk_test_');
    }

    public static function driverLabel(): string
    {
        return match (config('payments.driver')) {
            'demo' => 'تفعيل فوري (تجريبي)',
            'stripe' => self::isStripeTestMode() ? 'Stripe تجريبي' : 'Stripe',
            'tap' => 'Tap',
            default => 'تحويل بنكي',
        };
    }
}
