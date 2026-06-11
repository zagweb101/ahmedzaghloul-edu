<?php

return [

    'driver' => env('PAYMENT_DRIVER', 'manual'),

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

];
