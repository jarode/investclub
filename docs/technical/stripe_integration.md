# Integracja Stripe

## 1. Komponenty Stripe

### Identity Verification (KYC)
- Weryfikacja dokumentów z ponad 100 krajów
- Biometryczna weryfikacja tożsamości (selfie)
- Automatyczna walidacja dokumentów
- Zgodność z wymogami AML/KYC
- Koszt: $1.50 za weryfikację (pierwsze 50 darmowe)

### Billing & Subscriptions
- Zarządzanie planami subskrypcyjnymi
- Automatyczne płatności cykliczne
- Faktury i raporty
- Obsługa różnych walut
- Zarządzanie kartami

### Connect (dla marketplace)
- Weryfikacja sprzedających (projekty)
- Zarządzanie wypłatami
- Split payments
- Raportowanie podatkowe

## 2. Implementacja

### Konfiguracja Stripe
```php
// config/services.php
return [
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ]
];
```

### Model User z integracją Stripe
```php
class User extends Authenticatable
{
    use HasStripeCustomer;
    
    protected $fillable = [
        'stripe_id',
        'stripe_verification_status',
        'subscription_status',
    ];

    public function startVerification()
    {
        $verification = \Stripe\Identity\VerificationSession::create([
            'type' => 'document',
            'metadata' => [
                'user_id' => $this->id
            ]
        ]);

        return $verification->url;
    }

    public function createOrGetStripeCustomer()
    {
        if ($this->stripe_id) {
            return \Stripe\Customer::retrieve($this->stripe_id);
        }

        $customer = \Stripe\Customer::create([
            'email' => $this->email,
            'metadata' => [
                'user_id' => $this->id
            ]
        ]);

        $this->update(['stripe_id' => $customer->id]);
        return $customer;
    }
}
```

### Subskrypcje
```php
class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $user = $request->user();
        
        // Sprawdź czy użytkownik przeszedł weryfikację
        if ($user->stripe_verification_status !== 'verified') {
            return response()->json(['error' => 'Verification required'], 403);
        }

        try {
            $subscription = $user->newSubscription('default', $request->plan_id)
                ->create($request->payment_method);

            return response()->json([
                'subscription' => $subscription,
                'status' => 'active'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

### Webhooks
```php
class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $event = \Stripe\Event::constructFrom($payload);

        switch ($event->type) {
            case 'identity.verification_session.completed':
                $this->handleVerificationComplete($event->data->object);
                break;
                
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdate($event->data->object);
                break;
        }

        return response()->json(['status' => 'processed']);
    }

    private function handleVerificationComplete($session)
    {
        $user = User::where('stripe_id', $session->metadata->user_id)->first();
        $user->update([
            'stripe_verification_status' => $session->status,
            'verified_at' => now()
        ]);
    }
}
```

## 3. Plan Wdrożenia

### Etap 1: Podstawowa Integracja (3-4 dni)
1. Konfiguracja konta Stripe
2. Instalacja SDK
3. Implementacja podstawowych modeli
4. Konfiguracja webhooków

### Etap 2: Weryfikacja KYC (4-5 dni)
1. Implementacja Stripe Identity
2. Flow weryfikacji użytkownika
3. Obsługa callbacków
4. Testy weryfikacji

### Etap 3: Subskrypcje (3-4 dni)
1. Konfiguracja planów w Stripe
2. Implementacja systemu subskrypcji
3. Integracja z systemem uprawnień
4. Testy płatności

### Etap 4: Marketplace (5-6 dni)
1. Konfiguracja Stripe Connect
2. Weryfikacja projektów/sprzedających
3. System wypłat
4. Testy marketplace

## 4. Wymagane Pakiety
```json
{
    "require": {
        "stripe/stripe-php": "^10.0",
        "laravel/cashier": "^14.0"
    }
}
```

## 5. Środowisko
```env
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
CASHIER_CURRENCY=eur
CASHIER_CURRENCY_LOCALE=en
```

## 6. Migracje
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('stripe_id')->nullable()->index();
    $table->string('stripe_verification_status')->nullable();
    $table->timestamp('verified_at')->nullable();
    $table->string('pm_type')->nullable();
    $table->string('pm_last_four', 4)->nullable();
    $table->timestamp('trial_ends_at')->nullable();
});

Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id');
    $table->string('name');
    $table->string('stripe_id')->unique();
    $table->string('stripe_status');
    $table->string('stripe_price')->nullable();
    $table->integer('quantity')->nullable();
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('ends_at')->nullable();
    $table->timestamps();
});
``` 