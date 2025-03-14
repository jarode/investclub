# Minimalne Wymagania dla POC na Laravel Cloud

## 1. Podstawowa Architektura

### Kluczowe komponenty:
- Laravel 11 z Jetstream + Livewire
- Teams (dla rozróżnienia inwestorów i właścicieli projektów)
- Podstawowa weryfikacja użytkowników
- SQLite jako baza danych (na potrzeby POC)

### Zestaw pakietów dla POC:
```bash
# Podstawowa instalacja Laravel 
composer create-project laravel/laravel:^11.0 investclub
cd investclub

# Instalacja Jetstream z Livewire i Teams
composer require laravel/jetstream
php artisan jetstream:install livewire --teams

# Podstawowe narzędzia
composer require spatie/laravel-medialibrary
composer require laravel-lang/common
composer require predis/predis
```

## 2. Minimalna Struktura Bazy Danych

### Dodatkowe pola dla User:
```php
// database/migrations/add_custom_fields_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('kyc_status')->default('pending');
    $table->timestamp('kyc_verified_at')->nullable();
    $table->string('preferred_language')->default('en');
    $table->json('investment_preferences')->nullable();
});
```

### Projekt - absolutne minimum:
```php
// database/migrations/create_projects_table.php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained();
    $table->string('name');
    $table->text('description');
    $table->decimal('investment_amount', 15, 2);
    $table->string('status')->default('draft');
    $table->timestamps();
});
```

## 3. Podstawowe Funkcjonalności POC

### Funkcjonalności dla MVP:
1. **Rejestracja i logowanie** (gotowe z Jetstream)
2. **Podstawowa weryfikacja KYC** (ręczna dla POC)
3. **Tworzenie i przeglądanie projektów**
4. **Prostej wyszukiwarka projektów**
5. **Podstawowe profile użytkowników**
6. **Wielojęzyczność (PL/EN)**

### Minimalne komponenty Livewire:
```bash
# Generowanie komponentów
php artisan make:livewire Projects/CreateProject
php artisan make:livewire Projects/ProjectList
php artisan make:livewire Projects/ProjectDetails
php artisan make:livewire Dashboard/UserDashboard
```

## 4. Minimalna Implementacja

### Struktura projektu na POC:
```
app/
├── Http/
│   ├── Livewire/
│   │   ├── Projects/
│   │   │   ├── CreateProject.php
│   │   │   ├── ProjectList.php
│   │   │   └── ProjectDetails.php
│   │   └── Dashboard/
│   │       └── UserDashboard.php
│   └── Controllers/
│       └── LanguageController.php
├── Models/
│   ├── User.php (rozszerzony)
│   ├── Team.php (rozszerzony)
│   └── Project.php
├── Providers/
│   └── AppServiceProvider.php
└── Actions/
    └── Jetstream/ (rozszerzone)
```

### Model Projektu (minimum):
```php
// app/Models/Project.php
class Project extends Model
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'team_id',
        'name', 
        'description',
        'investment_amount',
        'status'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
    }
}
```

## 5. Konfiguracja dla Laravel Cloud

### Minimalna konfiguracja:
```php
// .env.cloud
APP_ENV=production
APP_DEBUG=false
APP_URL=https://investclub.laravel.app

# Baza danych (SQLite dla POC)
DB_CONNECTION=sqlite

# Cache i sesje (Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Języki
SUPPORTED_LOCALES=en,pl
DEFAULT_LOCALE=en
```

### Minimalny plik GitHub Action:
```yaml
# .github/workflows/deploy.yml
name: Deploy to Laravel Cloud
on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          
      - name: Install Dependencies
        run: composer install --prefer-dist --no-interaction
        
      - name: Deploy to Laravel Cloud
        run: php artisan cloud:deploy
        env:
          LARAVEL_CLOUD_TOKEN: ${{ secrets.LARAVEL_CLOUD_TOKEN }}
```

## 6. Wdrożenie Minimalne POC

### Proces wdrożenia:
1. Instalacja Laravel z Jetstream (Teams)
2. Dodanie modelu Project i migracji
3. Implementacja podstawowych komponentów Livewire
4. Konfiguracja wielojęzyczności
5. Konfiguracja i wdrożenie do Laravel Cloud

### Estymowany czas wdrożenia POC:
- 2-3 dni robocze dla doświadczonego zespołu

## 7. Testy dla POC

### Minimalne testy funkcjonalne:
```bash
php artisan make:test Projects/BasicProjectTest
```

```php
// tests/Feature/Projects/BasicProjectTest.php
public function test_authenticated_user_can_create_project()
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->switchTeam($team);
    
    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'Test Project',
        'description' => 'Project description',
        'investment_amount' => 10000
    ]);
    
    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project'
    ]);
}
``` 