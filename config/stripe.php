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
    
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    
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
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
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
    | Customer Portal Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration for the Stripe Customer Portal.
    |
    */
    
    'customer_portal' => [
        'enabled' => env('STRIPE_CUSTOMER_PORTAL_ENABLED', true),
        'configuration_id' => env('STRIPE_CUSTOMER_PORTAL_CONFIGURATION_ID'),
        'headline' => env('STRIPE_CUSTOMER_PORTAL_HEADLINE', 'Zarządzanie subskrypcją InvestClub'),
        'privacy_policy_url' => env('STRIPE_CUSTOMER_PORTAL_PRIVACY_POLICY_URL'),
        'terms_url' => env('STRIPE_CUSTOMER_PORTAL_TERMS_URL'),
        'support_url' => env('STRIPE_CUSTOMER_PORTAL_SUPPORT_URL'),
    ],

    'products' => [
        'free_investor' => [
            'id' => env('STRIPE_FREE_INVESTOR_PRODUCT_ID'),
            'price_id' => env('STRIPE_FREE_INVESTOR_PRICE_ID'),
            'name' => 'I-Free',
            'description' => 'Darmowy plan dla inwestorów',
            'price' => 0,
            'features' => [
                'Przeglądanie projektów',
                'Wyrażanie zainteresowania',
                'Ograniczony dostęp do szczegółów'
            ]
        ],
        'premium_investor' => [
            'id' => env('STRIPE_PREMIUM_INVESTOR_PRODUCT_ID'),
            'price_id' => env('STRIPE_PREMIUM_INVESTOR_PRICE_ID'),
            'name' => 'I-Premium',
            'description' => 'Plan premium dla inwestorów',
            'price' => 500,
            'features' => [
                'Wszystko z planu I-Free',
                'Priorytetowy dostęp do nowych projektów',
                'Zaawansowane analizy i raporty',
                'Dostęp do ekskluzywnych projektów'
            ]
        ],
        'premium_owner' => [
            'id' => env('STRIPE_PREMIUM_OWNER_PRODUCT_ID'),
            'price_id' => env('STRIPE_PREMIUM_OWNER_PRICE_ID'),
            'name' => 'O-Premium',
            'description' => 'Plan premium dla właścicieli projektów',
            'price' => 1000,
            'features' => [
                'Możliwość dodawania projektów',
                'Dostęp do bazy inwestorów premium',
                'Narzędzia do analizy zainteresowania',
                'Wsparcie w procesie pozyskiwania finansowania'
            ]
        ]
    ],
]; 