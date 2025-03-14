# Przyspieszenie Developmentu z Jetstream

## 1. Gotowe Pakiety Laravel

### Frontend i UI
```bash
# Livewire PowerGrid dla tabel i list
composer require power-components/livewire-powergrid

# Wire UI dla komponentów
composer require wireui/wireui

# Spatie Media Library dla zarządzania plikami
composer require spatie/laravel-medialibrary
```

### Komunikacja i Powiadomienia
```bash
# Laravel WebSockets (zamiast Pusher)
composer require beyondcode/laravel-websockets
php artisan websockets:install

# Laravel Notifications
composer require laravel-notification-channels/telegram
composer require laravel-notification-channels/twilio

# Pakiet do wielojęzyczności
composer require laravel-lang/common
```

### Dokumenty i Pliki
```bash
# Generowanie PDF
composer require barryvdh/laravel-dompdf

# Eksport/Import
composer require maatwebsite/excel
```

### Monitoring i Debugowanie
```bash
# Telescope dla developmentu
composer require laravel/telescope --dev
php artisan telescope:install

# Horizon dla kolejek
composer require laravel/horizon
php artisan horizon:install
```

## 2. Rozszerzenie Komponentów Jetstream

### Dostosowanie Teams
```bash
# Generowanie komponentów Teams
php artisan jetstream:teams

# Dodanie typów Teams
php artisan make:model TeamType -m
```

```php
// app/Actions/Jetstream/CreateTeam.php
class CreateTeam extends DefaultCreateTeam
{
    public function create(User $user, array $input): Team
    {
        return DB::transaction(function () use ($user, $input) {
            return tap(Team::create([
                'user_id' => $user->id,
                'name' => $input['name'],
                'type' => $input['type'] ?? 'personal',
                'personal_team' => $input['type'] === 'personal',
            ]), function (Team $team) use ($user) {
                $this->addTeamMember($team, $user, 'owner', true);
            });
        });
    }
}
```

### Rozszerzenie Profilu
```bash
# Generowanie migracji dla dodatkowych pól
php artisan make:migration add_kyc_fields_to_users_table
```

```php
// app/Actions/Jetstream/UpdateUserProfileInformation.php
class UpdateUserProfileInformation extends DefaultUpdateUserProfileInformation
{
    public function update($user, array $input)
    {
        parent::update($user, $input);
        
        $user->investment_preferences = $input['investment_preferences'] ?? [];
        $user->preferred_language = $input['preferred_language'] ?? config('app.locale');
        $user->save();
    }
}
```

## 3. Integracja ze Stripe

### Podstawowa Konfiguracja
```bash
# Instalacja pakietu Stripe
composer require stripe/stripe-php
```

```php
// config/stripe.php
return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'webhook_tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
];
```

### Service Provider
```php
// app/Providers/StripeServiceProvider.php
class StripeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\Stripe\StripeClient::class, function ($app) {
            return new \Stripe\StripeClient(config('stripe.secret'));
        });
        
        $this->app->singleton(StripeService::class, function ($app) {
            return new StripeService($app->make(\Stripe\StripeClient::class));
        });
    }
}
```

### Serwis Stripe
```php
// app/Services/StripeService.php
class StripeService
{
    protected $stripe;
    
    public function __construct(\Stripe\StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }
    
    public function createCustomer(User $user): \Stripe\Customer
    {
        if ($user->stripe_id) {
            return $this->getCustomer($user->stripe_id);
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
                'team_id' => $user->currentTeam?->id
            ]
        ]);

        $user->stripe_id = $customer->id;
        $user->save();

        return $customer;
    }
    
    public function startVerification(User $user, string $returnUrl): \Stripe\Identity\VerificationSession
    {
        return $this->stripe->identity->verificationSessions->create([
            'type' => 'document',
            'metadata' => [
                'user_id' => $user->id
            ],
            'options' => [
                'document' => [
                    'allowed_types' => ['driving_license', 'passport', 'id_card'],
                    'require_matching_selfie' => true,
                ],
            ],
            'return_url' => $returnUrl,
        ]);
    }
}
```

## 4. Optymalizacja dla Laravel Cloud

### Konfiguracja Redis
```bash
# Instalacja Redis dla Laravel
composer require predis/predis
```

```php
// config/database.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
]
```

### Konfiguracja S3
```bash
# Instalacja pakietu AWS
composer require league/flysystem-aws-s3-v3
```

```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'throw' => false,
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        'throw' => false,
    ],
]
```

### Kolejki i WebSockets
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'redis'),

// config/broadcasting.php
'default' => env('BROADCAST_DRIVER', 'redis'),
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
]
```

## 5. Testowanie

### Konfiguracja Testów
```bash
# Generowanie fabryki dla projektu
php artisan make:factory ProjectFactory

# Generowanie testów
php artisan make:test Projects/CreateProjectTest
php artisan make:test StripeServiceTest --unit
```

```php
// tests/TestCase.php
class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        Notification::fake();
        Event::fake();
    }
}
```

### GitHub CI dla Testów
```yaml
# .github/workflows/run-tests.yml
name: Run Tests
on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      redis:
        image: redis
        ports:
          - 6379:6379
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite
          
      - name: Install Composer Dependencies
        run: composer install --prefer-dist --no-interaction
        
      - name: Create Database
        run: |
          mkdir -p database
          touch database/database.sqlite
          
      - name: Run Tests
        env:
          DB_CONNECTION: sqlite
          DB_DATABASE: database/database.sqlite
        run: php artisan test
``` 