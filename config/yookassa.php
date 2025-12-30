<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */
    
    'mode' => env('YOOKASSA_MODE', 'sandbox'),
    
    'sandbox' => [
        'shop_id' => env('YOOKASSA_SHOP_ID'),
        'secret_key' => env('YOOKASSA_SECRET_KEY'),
    ],
    
    'production' => [
        'shop_id' => env('YOOKASSA_SHOP_ID'),
        'secret_key' => env('YOOKASSA_SECRET_KEY'),
    ],
    
    'webhook_url' => env('APP_URL') . '/payment/webhook',
    
    'defaults' => [
        'currency' => 'RUB',
        'vat_code' => 1,
        'payment_method' => 'bank_card',
        'capture' => true,
    ],
];