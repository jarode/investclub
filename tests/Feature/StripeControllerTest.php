<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Stripe\StripeClient;
use PHPUnit\Framework\Attributes\Test;

class StripeControllerTest extends TestCase
{
    use WithFaker, DatabaseMigrations;

    protected $user;
    protected $stripeMock;
    protected $checkoutMock;
    protected $sessionsMock;
    protected $customersMock;
    protected $subscriptionsMock;
    protected $billingPortalMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tworzymy użytkownika testowego
        $this->user = User::factory()->create([
            'stripe_customer_id' => 'cus_test123',
            'stripe_subscription_id' => 'sub_test123',
            'stripe_subscription_status' => 'active',
            'plan_type' => 'free',
            'cancellation_requested' => false
        ]);

        // Mockujemy klienta Stripe
        $this->stripeMock = Mockery::mock('Stripe\StripeClient');
        $this->app->instance('Stripe\StripeClient', $this->stripeMock);

        // Mockujemy BillingPortal
        $this->billingPortalMock = Mockery::mock('Stripe\BillingPortal\Session');
        $this->stripeMock->shouldReceive('billingPortal')->andReturn(
            Mockery::mock()->shouldReceive('sessions')->andReturn(
                Mockery::mock()->shouldReceive('create')->andReturn($this->billingPortalMock)->getMock()
            )->getMock()
        );

        // Mockujemy Subscriptions
        $this->subscriptionsMock = Mockery::mock('Stripe\Service\SubscriptionService');
        $this->stripeMock->shouldReceive('subscriptions')->andReturn($this->subscriptionsMock);
        
        // Mockujemy checkout
        $this->checkoutMock = \Mockery::mock();
        $this->sessionsMock = \Mockery::mock();
        $this->checkoutMock->sessions = $this->sessionsMock;
        $this->stripeMock->checkout = $this->checkoutMock;
        
        // Mockujemy customers
        $this->customersMock = \Mockery::mock();
        $this->stripeMock->customers = $this->customersMock;
    }

    #[Test]
    public function it_can_handle_free_plan_activation()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/subscription/checkout', [
                'price_id' => config('stripe.free_investor_price_id')
            ]);

        $response->assertStatus(200)
            ->assertJson(['url' => route('dashboard')]);

        $this->user->refresh();
        $this->assertEquals('active', $this->user->stripe_subscription_status);
        $this->assertEquals('free', $this->user->plan_type);
    }

    #[Test]
    public function it_can_handle_premium_investor_plan_activation()
    {
        // Mockujemy sesję Stripe
        $session = new \stdClass();
        $session->id = 'cs_test123';
        $session->url = 'https://checkout.stripe.com/test';
        $session->customer = 'cus_test123';
        $session->subscription = 'sub_test123';

        // Mockujemy tworzenie sesji checkout
        $this->sessionsMock->shouldReceive('create')
            ->once()
            ->with(\Mockery::on(function ($params) {
                return $params['customer'] === 'cus_test123' &&
                       $params['line_items'][0]['price'] === config('stripe.premium_investor_price_id') &&
                       $params['mode'] === 'subscription';
            }))
            ->andReturn($session);

        $response = $this->actingAs($this->user)
            ->postJson('/subscription/checkout', [
                'price_id' => config('stripe.premium_investor_price_id')
            ]);

        $response->assertStatus(200)
            ->assertJson(['url' => 'https://checkout.stripe.com/test']);

        // Sprawdzamy czy ID sesji zostało zapisane
        $this->user->refresh();
        $this->assertEquals('cs_test123', $this->user->checkout_session_id);
    }

    #[Test]
    public function it_handles_checkout_session_creation_error()
    {
        $this->sessionsMock->shouldReceive('create')
            ->once()
            ->andThrow(new \Stripe\Exception\InvalidRequestException('Błąd żądania', 400));

        $response = $this->actingAs($this->user)
            ->postJson('/subscription/checkout', [
                'price_id' => config('stripe.premium_investor_price_id')
            ]);

        $response->assertStatus(500)
            ->assertJson(['error' => 'Nie udało się utworzyć sesji płatności']);
    }

    #[Test]
    public function it_can_handle_checkout_success()
    {
        // Mockujemy sesję Stripe
        $session = new \stdClass();
        $session->customer = 'cus_test123';
        $session->subscription = 'sub_test123';
        $session->subscription = new \stdClass();
        $session->subscription->id = 'sub_test123';
        $session->subscription->status = 'active';
        $session->subscription->items = new \stdClass();
        $session->subscription->items->data = [
            (object)[
                'price' => (object)[
                    'id' => config('stripe.premium_investor_price_id')
                ]
            ]
        ];

        // Mockujemy pobieranie sesji
        $this->sessionsMock->shouldReceive('retrieve')
            ->with('cs_test123', ['expand' => ['subscription']])
            ->once()
            ->andReturn($session);

        $this->user->update(['checkout_session_id' => 'cs_test123']);

        $response = $this->actingAs($this->user)
            ->get(route('subscription.success', ['session_id' => 'cs_test123']));

        $response->assertRedirect(route('dashboard'));

        $this->user->refresh();
        $this->assertEquals('active', $this->user->stripe_subscription_status);
        $this->assertEquals('premium-investor', $this->user->plan_type);
        $this->assertEquals('sub_test123', $this->user->stripe_subscription_id);
    }

    #[Test]
    public function it_can_handle_subscription_cancellation()
    {
        // Mockujemy subskrypcję Stripe
        $subscription = \Mockery::mock('Stripe\Subscription');
        $subscription->shouldReceive('__get')->with('status')->andReturn('active');
        $subscription->shouldReceive('__get')->with('current_period_end')->andReturn(time() + 86400);
        $subscription->shouldReceive('__get')->with('id')->andReturn('sub_test123');

        // Mockujemy pobieranie subskrypcji
        $this->subscriptionsMock->shouldReceive('retrieve')
            ->with($this->user->stripe_subscription_id)
            ->once()
            ->andReturn($subscription);

        // Mockujemy aktualizację subskrypcji
        $this->subscriptionsMock->shouldReceive('update')
            ->with($this->user->stripe_subscription_id, [
                'cancel_at_period_end' => true
            ])
            ->once()
            ->andReturn($subscription);

        // Upewniamy się, że użytkownik ma ustawione cancellation_requested na false
        $this->user->update(['cancellation_requested' => false]);
        \Log::info('Stan użytkownika przed anulowaniem', ['user' => $this->user->toArray()]);

        $response = $this->actingAs($this->user)
            ->post(route('subscription.cancel'));

        $response->assertRedirect(route('dashboard'));

        $this->user->refresh();
        \Log::info('Stan użytkownika po anulowaniu', ['user' => $this->user->toArray()]);
        $this->assertTrue($this->user->cancellation_requested);
        $this->assertEquals('active', $this->user->stripe_subscription_status);
    }

    #[Test]
    public function it_validates_price_id_for_checkout()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/subscription/checkout', [
                'price_id' => 'invalid_price_id'
            ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Nieprawidłowy identyfikator planu']);
    }

    #[Test]
    public function it_can_create_billing_portal_session()
    {
        // Mockujemy sesję portalu
        $portalSession = new \stdClass();
        $portalSession->url = 'https://billing.stripe.com/test';

        // Mockujemy tworzenie sesji portalu
        $this->billingPortalMock->shouldReceive('create')
            ->once()
            ->with([
                'customer' => $this->user->stripe_customer_id,
                'return_url' => route('dashboard')
            ])
            ->andReturn($portalSession);

        $response = $this->actingAs($this->user)
            ->post(route('billing.portal'));

        $response->assertStatus(200)
            ->assertJson(['url' => 'https://billing.stripe.com/test']);
    }

    #[Test]
    public function it_handles_portal_session_creation_error()
    {
        // Mockujemy błąd Stripe
        $this->stripeMock->billingPortal->sessions->shouldReceive('create')
            ->once()
            ->andThrow(new \Stripe\Exception\InvalidRequestException('Błąd API', 400));

        $response = $this->actingAs($this->user)
            ->postJson(route('billing.portal'));

        $response->assertStatus(500)
            ->assertJson(['error' => 'Nie udało się utworzyć sesji portalu. Spróbuj ponownie później.']);
    }

    #[Test]
    public function it_validates_user_has_stripe_customer_id_for_portal()
    {
        // Usuwamy stripe_customer_id
        $this->user->update(['stripe_customer_id' => null]);

        $response = $this->actingAs($this->user)
            ->post(route('billing.portal'));

        $response->assertStatus(400)
            ->assertJson(['error' => 'Nie znaleziono identyfikatora klienta Stripe.']);
    }

    #[Test]
    public function it_handles_subscription_update_error()
    {
        // Mockujemy błąd podczas aktualizacji subskrypcji
        $this->subscriptionsMock->shouldReceive('update')
            ->once()
            ->andThrow(new \Stripe\Exception\InvalidRequestException('Błąd aktualizacji', 400));

        $response = $this->actingAs($this->user)
            ->post(route('subscription.update'), [
                'price_id' => config('stripe.premium_investor_price_id')
            ]);

        $response->assertStatus(500)
            ->assertJson(['error' => 'Nie udało się zaktualizować subskrypcji. Spróbuj ponownie później.']);
    }

    #[Test]
    public function it_validates_subscription_status_before_update()
    {
        // Ustawiamy status subskrypcji na cancelled
        $this->user->update(['stripe_subscription_status' => 'cancelled']);

        $response = $this->actingAs($this->user)
            ->post(route('subscription.update'), [
                'price_id' => config('stripe.premium_investor_price_id')
            ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Nie można zaktualizować anulowanej subskrypcji.']);
    }

    #[Test]
    public function it_can_create_portal_session()
    {
        $user = User::factory()->create([
            'stripe_customer_id' => 'cus_test123'
        ]);

        $this->actingAs($user)
            ->post(route('stripe.portal'));

        // Assertions...
    }

    #[Test]
    public function test_it_returns_error_for_missing_customer_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('stripe.portal'));

        $response->assertStatus(400);
    }

    #[Test]
    public function test_it_handles_stripe_error()
    {
        $user = User::factory()->create([
            'stripe_customer_id' => 'invalid_id'
        ]);

        $response = $this->actingAs($user)
            ->post(route('stripe.portal'));

        $response->assertStatus(500);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 