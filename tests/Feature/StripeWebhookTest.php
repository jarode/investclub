<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Stripe\Event;
use PHPUnit\Framework\Attributes\Test;

class StripeWebhookTest extends TestCase
{
    use WithFaker, DatabaseMigrations;

    protected $user;
    protected $stripeSecret;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'stripe_customer_id' => 'cus_test123',
            'stripe_subscription_id' => 'sub_test123',
            'stripe_subscription_status' => 'active',
            'plan_type' => 'free'
        ]);

        $this->stripeSecret = config('stripe.webhook.secret');
    }

    #[Test]
    public function it_can_handle_checkout_session_completed_webhook()
    {
        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'mode' => 'subscription',
                    'customer' => 'cus_test123',
                    'subscription' => 'sub_test123',
                    'metadata' => [
                        'user_id' => $this->user->id,
                        'price_id' => config('stripe.premium_investor_price_id')
                    ],
                    'subscription' => [
                        'items' => [
                            'data' => [
                                [
                                    'price' => [
                                        'id' => config('stripe.premium_investor_price_id')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertEquals('active', $this->user->stripe_subscription_status);
        $this->assertEquals('premium-investor', $this->user->plan_type);
        $this->assertNotNull($this->user->stripe_subscription_id);
        $this->assertNotNull($this->user->stripe_customer_id);
    }

    #[Test]
    public function it_can_handle_subscription_updated_webhook()
    {
        $payload = [
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_test123',
                    'status' => 'active',
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => config('stripe.premium_owner_price_id')
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertEquals('active', $this->user->stripe_subscription_status);
        $this->assertEquals('premium-owner', $this->user->plan_type);
        $this->assertNotNull($this->user->stripe_subscription_id);
    }

    #[Test]
    public function it_can_handle_subscription_cancelled_webhook()
    {
        $payload = [
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => 'sub_test123',
                    'status' => 'canceled',
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => config('stripe.premium_investor_price_id')
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertEquals('cancelled', $this->user->stripe_subscription_status);
        $this->assertTrue($this->user->cancellation_requested);
        $this->assertNull($this->user->stripe_subscription_id);
    }

    #[Test]
    public function it_validates_stripe_signature()
    {
        $response = $this->withHeaders([
            'Stripe-Signature' => 'invalid_signature'
        ])->postJson(route('webhook.subscription'), ['type' => 'test']);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Nieprawidłowy podpis Stripe.']);
    }

    #[Test]
    public function it_can_handle_invoice_payment_failed_webhook()
    {
        $payload = [
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'customer' => 'cus_test123',
                    'subscription' => 'sub_test123',
                    'attempt_count' => 1,
                    'next_payment_attempt' => time() + 86400
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertEquals('past_due', $this->user->stripe_subscription_status);
        $this->assertNotNull($this->user->last_payment_failed_at);
    }

    #[Test]
    public function it_can_handle_subscription_trial_will_end_webhook()
    {
        $payload = [
            'type' => 'customer.subscription.trial_will_end',
            'data' => [
                'object' => [
                    'id' => 'sub_test123',
                    'customer' => 'cus_test123',
                    'trial_end' => time() + 86400
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        // Sprawdzamy, czy użytkownik został powiadomiony
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $this->user->id,
            'type' => 'App\Notifications\SubscriptionTrialEnding'
        ]);
    }

    #[Test]
    public function it_can_handle_subscription_created_webhook()
    {
        $payload = [
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_test123',
                    'customer' => 'cus_test123',
                    'status' => 'active',
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => config('stripe.premium_investor_price_id')
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertEquals('active', $this->user->stripe_subscription_status);
        $this->assertEquals('premium-investor', $this->user->plan_type);
        $this->assertNotNull($this->user->stripe_subscription_id);
        $this->assertNotNull($this->user->stripe_customer_id);
    }

    #[Test]
    public function it_can_handle_subscription_deleted_webhook()
    {
        $payload = [
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => 'sub_test123',
                    'customer' => 'cus_test123',
                    'status' => 'canceled',
                    'canceled_at' => time()
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        
        $this->user->refresh();
        $this->assertEquals('cancelled', $this->user->stripe_subscription_status);
        $this->assertEquals('free', $this->user->plan_type);
        $this->assertNotNull($this->user->subscription_cancelled_at);
        $this->assertNull($this->user->stripe_subscription_id);
    }

    #[Test]
    public function it_handles_unknown_webhook_event()
    {
        $payload = [
            'type' => 'unknown.event',
            'data' => [
                'object' => []
            ]
        ];

        $response = $this->withHeaders([
            'Stripe-Signature' => $this->generateStripeSignature($payload)
        ])->postJson(route('webhook.subscription'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Webhook received but no action taken.']);
    }

    protected function generateStripeSignature($payload)
    {
        $timestamp = time();
        $signedPayload = $timestamp . '.' . json_encode($payload);
        $signature = hash_hmac('sha256', $signedPayload, $this->stripeSecret);
        return "t={$timestamp},v1={$signature}";
    }
} 