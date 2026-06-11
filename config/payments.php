<?php

return [

    'driver' => env('PAYMENT_DRIVER', 'manual'),

    /*
    |--------------------------------------------------------------------------
    | Demo / Test Mode (before commercial launch)
    |--------------------------------------------------------------------------
    |
    | When enabled, shows a site-wide banner and uses demo bank details.
    | Use PAYMENT_DRIVER=demo for instant fake purchases, or stripe with
    | sk_test_* keys for Stripe Checkout test mode.
    |
    */

    'demo_mode' => (bool) env('PAYMENT_DEMO_MODE', false),

    'demo_bank' => [
        'bank_name' => env('PAYMENT_DEMO_BANK_NAME', 'بنك تجريبي — لا تحوّل مبلغًا حقيقيًا'),
        'account_name' => env('PAYMENT_DEMO_ACCOUNT_NAME', 'أكاديمية أحمد زغلول (حساب تجريبي)'),
        'iban' => env('PAYMENT_DEMO_IBAN', 'SA00 8000 0000 6080 0000 0001'),
        'instructions' => env(
            'PAYMENT_DEMO_INSTRUCTIONS',
            'وضع تجريبي: هذه بيانات وهمية للعرض فقط. للتجربة السريعة استخدم «تفعيل الاشتراك الآن» (demo) أو بطاقة Stripe التجريبية 4242 4242 4242 4242.',
        ),
    ],

    'manual' => [
        'bank_name' => env('PAYMENT_BANK_NAME', 'البنك السعودي الفرنسي'),
        'account_name' => env('PAYMENT_ACCOUNT_NAME', 'بيت المصور'),
        'iban' => env('PAYMENT_IBAN', 'SA00 0000 0000 0000 0000 0000'),
        'instructions' => env(
            'PAYMENT_MANUAL_INSTRUCTIONS',
            'حوّل مبلغ الاشتراك ثم أرسل رقم الطلب في ملاحظة التحويل. سيتم تفعيل حسابك بعد المراجعة.',
        ),
    ],

    'tap' => [
        'secret_key' => env('TAP_SECRET_KEY'),
        'api_url' => env('TAP_API_URL', 'https://api.tap.company/v2'),
        'source_id' => env('TAP_SOURCE_ID', 'src_all'),
    ],

    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'api_url' => env('STRIPE_API_URL', 'https://api.stripe.com/v1'),
    ],

];
