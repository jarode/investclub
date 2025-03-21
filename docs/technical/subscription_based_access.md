# Model dostępu oparty na subskrypcjach w InvestClub

## Wprowadzenie

Dokument opisuje nowe podejście do zarządzania dostępem w systemie InvestClub, oparte na pakietach subskrypcji zamiast na tradycyjnych rolach użytkowników. Takie podejście pozwala na większą elastyczność i lepsze dopasowanie do potrzeb biznesowych platformy.

## Aktualny model oparty na rolach

W dotychczasowym podejściu dostęp do funkcjonalności był kontrolowany przez przypisane role użytkowników:

- `admin` - pełny dostęp do wszystkich funkcji systemu
- `investor` - dostęp do przeglądania projektów i inwestowania po weryfikacji KYC i aktywnej subskrypcji
- `project_owner` (lub `manager`) - dostęp do tworzenia i zarządzania projektami

Problemy tego podejścia:
- Użytkownik mógł mieć tylko jedną rolę
- Zmiana roli wymagała interwencji administratora
- Sztywny podział utrudniał elastyczne korzystanie z platformy

## Nowy model oparty na subskrypcjach

W nowym modelu dostęp do funkcjonalności wynika z aktywnego pakietu subskrypcji użytkownika:

### Pakiety subskrypcji

1. **I-Free (Basic Investor)**
   - Darmowy plan dostępny dla wszystkich użytkowników
   - Dostęp do podstawowych funkcji platformy
   - Wymaga weryfikacji KYC
   - Ograniczony dostęp do szczegółów projektów

2. **I-Premium (Premium Investor)**
   - Płatny plan dla inwestorów (500 zł / miesiąc)
   - Pełny dostęp do funkcji dla inwestorów
   - Priorytetowy dostęp do nowych projektów
   - Zaawansowane analizy i raporty
   - Dostęp do ekskluzywnych projektów

3. **O-Premium (Project Owner)**
   - Płatny plan dla właścicieli projektów (1000 zł / miesiąc)
   - Wszystkie funkcje planu I-Premium
   - Możliwość dodawania i zarządzania własnymi projektami
   - Dostęp do bazy inwestorów premium
   - Narzędzia analityczne dla właścicieli projektów

### Zalety nowego podejścia

1. **Elastyczność** - użytkownik może swobodnie zmieniać swój pakiet bez zmiany konta
2. **Płynne przejścia** - inwestor może w dowolnym momencie stać się właścicielem projektu
3. **Prosty model biznesowy** - ceny i funkcje są jasno określone
4. **Automatyzacja** - zmiana uprawnień następuje natychmiastowo po zmianie pakietu
5. **Personalizacja** - użytkownik sam decyduje o swoim poziomie dostępu

## Techniczna implementacja

### 1. Modyfikacja modelu User

```php
// app/Models/User.php

// Dodanie nowych metod pomocniczych:
public function canManageProjects(): bool
{
    // Użytkownik może zarządzać projektami, jeśli ma plan O-Premium
    return $this->hasActiveSubscription() && 
           $this->plan_type === 'premium-owner';
}

public function hasFullAccess(): bool
{
    // Pełny dostęp mają administratorzy lub użytkownicy z planem O-Premium
    return $this->isAdmin() || 
          ($this->hasActiveSubscription() && $this->plan_type === 'premium-owner');
}

// Aktualizacja istniejących metod:
public function hasRole(string $role): bool
{
    // Dla roli 'project_owner' sprawdzamy subskrypcję O-Premium
    if (strtolower($role) === 'project_owner' || strtolower($role) === 'manager') {
        return $this->canManageProjects();
    }
    
    // Dla innych ról zachowujemy stare zachowanie
    return strtolower($this->role) === strtolower($role);
}
```

### 2. Middleware do sprawdzania typu subskrypcji

```php
// app/Http/Middleware/CheckSubscriptionPlan.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscriptionPlan
{
    public function handle(Request $request, Closure $next, string $planType): mixed
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Administratorzy mają dostęp do wszystkiego
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Dla właścicieli projektów
        if ($planType === 'owner' && !$user->canManageProjects()) {
            return redirect()->route('subscription')
                ->with('warning', 'Ta funkcja wymaga pakietu O-Premium.');
        }
        
        // Dla inwestorów premium
        if ($planType === 'premium' && 
            (!$user->hasActiveSubscription() || $user->plan_type === 'free')) {
            return redirect()->route('subscription')
                ->with('warning', 'Ta funkcja wymaga pakietu Premium.');
        }
        
        return $next($request);
    }
}
```

### 3. Aktualizacja tras w web.php

```php
// routes/web.php

// Trasy dla właścicieli projektów
Route::middleware(['auth', 'verified.kyc', 'subscription.plan:owner'])->group(function () {
    Route::resource('my-projects', ProjectController::class);
    Route::get('/project-dashboard', [ProjectDashboardController::class, 'index'])
         ->name('project.dashboard');
});

// Trasy dla inwestorów premium
Route::middleware(['auth', 'verified.kyc', 'subscription.plan:premium'])->group(function () {
    Route::get('/exclusive-projects', [ProjectController::class, 'exclusive'])
         ->name('projects.exclusive');
});

// Rejestracja middleware
// bootstrap/app.php
$middleware->alias([
    'subscription.plan' => \App\Http\Middleware\CheckSubscriptionPlan::class,
]);
```

### 4. Aktualizacja widoku subskrypcji

Widok subskrypcji powinien jasno informować, jakie funkcje są dostępne w każdym planie:

```blade
<!-- resources/views/stripe/subscription.blade.php -->
<!-- W opisie planu O-Premium: -->
<ul class="mb-6 space-y-2">
    <li class="flex items-center">
        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Wszystkie funkcje planu I-Premium
    </li>
    <li class="flex items-center">
        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <strong>Możliwość dodawania i zarządzania projektami</strong>
    </li>
    <!-- Pozostałe funkcje... -->
</ul>
```

### 5. Aktualizacja kontrolera StripeController

Logika zmiany pakietu powinna aktualizować uprawnienia:

```php
// app/Http/Controllers/StripeController.php
public function handleCheckoutSuccess(Request $request)
{
    // ... istniejący kod ...
    
    // Określenie typu planu i potencjalnej zmiany roli
    if (stripos($session->subscription->plan->id, config('stripe.products.premium_owner.price_id')) !== false) {
        $user->plan_type = 'premium-owner';
        
        // Informowanie użytkownika o nowych możliwościach
        $message = 'Subskrypcja została utworzona pomyślnie. Teraz możesz dodawać i zarządzać projektami.';
    } elseif (stripos($session->subscription->plan->id, config('stripe.products.premium_investor.price_id')) !== false) {
        $user->plan_type = 'premium-investor';
        $message = 'Subskrypcja została utworzona pomyślnie. Masz teraz dostęp do premium funkcji dla inwestorów.';
    } else {
        $user->plan_type = 'premium';
        $message = 'Subskrypcja została utworzona pomyślnie.';
    }
    
    $user->save();
    
    return redirect()->route('dashboard')->with('success', $message);
}
```

### 6. Aktualizacja polityk dostępu

```php
// app/Policies/ProjectPolicy.php
class ProjectPolicy
{
    public function create(User $user): bool
    {
        return $user->canManageProjects();
    }
    
    // Pozostałe metody...
}
```

## Zmiany w interfejsie użytkownika

### 1. Dashboard

Dashboard użytkownika powinien dynamicznie dostosowywać się do aktywnej subskrypcji:

```blade
<!-- resources/views/dashboard.blade.php -->

<!-- Sekcja dla właścicieli projektów -->
@if(auth()->user()->canManageProjects())
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium mb-4">{{ __('Zarządzanie projektami') }}</h3>
        <p class="mb-4">
            Jako użytkownik z pakietem O-Premium, możesz dodawać i zarządzać projektami inwestycyjnymi.
        </p>
        <div class="mt-4">
            <a href="{{ route('my-projects.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Dodaj nowy projekt
            </a>
            <a href="{{ route('my-projects.index') }}" class="ml-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Zarządzaj projektami
            </a>
        </div>
    </div>
@endif
```

### 2. Menu nawigacyjne

Menu powinno pokazywać odpowiednie opcje w zależności od subskrypcji:

```blade
<!-- resources/views/navigation-menu.blade.php -->

<!-- Sekcja dla właścicieli projektów -->
@if(auth()->user()->canManageProjects())
    <x-jet-nav-link href="{{ route('my-projects.index') }}" :active="request()->routeIs('my-projects.*')">
        {{ __('Moje projekty') }}
    </x-jet-nav-link>
@endif
```

## Proces migracji

1. Dodać kolumnę `plan_type` do tabeli `users` jeśli jeszcze nie istnieje
2. Utworzyć i zarejestrować nowe middleware `CheckSubscriptionPlan`
3. Dodać nowe metody pomocnicze do modelu User
4. Zaktualizować polityki dostępu
5. Zmodyfikować widoki, aby uwzględniały nowy model dostępu
6. Zaktualizować trasy w pliku `web.php`
7. Przetestować wszystkie ścieżki użytkownika

## Podsumowanie

Nowy model dostępu oparty na subskrypcjach pozwala użytkownikom pełnić różne role w systemie w zależności od wybranego pakietu. Inwestor może w dowolnym momencie stać się właścicielem projektu, aktywując odpowiedni pakiet subskrypcji, bez konieczności zmiany konta czy interwencji administratora.

To podejście jest bardziej elastyczne i lepiej odpowiada na potrzeby użytkowników, którzy mogą chcieć korzystać z platformy w różny sposób w zależności od swoich aktualnych potrzeb. 