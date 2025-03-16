<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key. These keys are required for
    | making API requests to Stripe.
    |
    */
    
    'key' => env('STRIPE_KEY', ''),
    'secret' => env('STRIPE_SECRET', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | The webhook secret is used to verify that webhook requests are coming
    | from Stripe.
    |
    */
    
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | The currency used for Stripe payments.
    |
    */
    
    'currency' => env('STRIPE_CURRENCY', 'pln'),
    
    /*
    |--------------------------------------------------------------------------
    | Product & Price IDs
    |--------------------------------------------------------------------------
    |
    | The IDs for your Stripe products and prices.
    |
    */
    
    'products' => [
        'free_investor' => [
            'price_id' => env('STRIPE_FREE_INVESTOR_PRICE_ID', 'price_1R3LLrBTxeaIpqRB1gppSxzK'),
            'product_id' => env('STRIPE_FREE_INVESTOR_PRODUCT_ID', 'prod_RxFr1ajRyqgFqa'),
        ],
        'premium_investor' => [
            'price_id' => env('STRIPE_PREMIUM_INVESTOR_PRICE_ID', 'price_1R3LMKBTxeaIpqRBHDccX2U1'),
            'product_id' => env('STRIPE_PREMIUM_INVESTOR_PRODUCT_ID', 'prod_RxFs58AVJVHqx2'),
        ],
        'premium_owner' => [
            'price_id' => env('STRIPE_PREMIUM_OWNER_PRICE_ID', 'price_1R3Lc4BTxeaIpqRBtNBnHwcH'),
            'product_id' => env('STRIPE_PREMIUM_OWNER_PRODUCT_ID', 'prod_RxG8yaXSS7WZoE'),
        ],
    ],
]; 