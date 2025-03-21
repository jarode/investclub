# Middleware i polityki dostępu w InvestClub

## Wprowadzenie

W aplikacji InvestClub stosujemy dwa mechanizmy kontroli dostępu:
1. **Middleware** - kontroluje dostęp na poziomie trasy
2. **Polityki (policies)** - zarządzają dostępem na poziomie zasobu

Ten dokument opisuje, jak używamy obu tych mechanizmów oraz jak je integrujemy z nowym modelem dostępu opartym na subskrypcjach.

## Middleware w Laravel 11

W Laravel 11 middleware są rejestrowane przy użyciu klasy `Illuminate\Foundation\Configuration\Middleware`. Oto przykład, jak rejestrujemy middleware w projekcie:

```php
// bootstrap/app.php
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'verified.kyc' => \App\Http\Middleware\EnsureKycIsVerified::class,
    'subscription.plan' => \App\Http\Middleware\CheckSubscriptionPlan::class,
]);
```

### Przykład middleware do weryfikacji roli

```php
// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return redirect()->route('dashboard')
                ->with('error', 'Nie masz uprawnień do tej sekcji.');
        }

        return $next($request);
    }
}
```

### Nowe middleware do weryfikacji planu subskrypcji

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

### Middleware weryfikacji KYC

```php
// app/Http/Middleware/EnsureKycIsVerified.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureKycIsVerified
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->user() || !$request->user()->hasVerifiedKyc()) {
            return redirect()->route('kyc.start')
                ->with('warning', 'Musisz przejść weryfikację KYC, aby kontynuować.');
        }

        return $next($request);
    }
}
```

## Polityki w Laravel Jetstream

Polityki kontrolują dostęp do poszczególnych zasobów. W systemie Laravel Jetstream, polityki są automatycznie powiązane z modelami na podstawie konwencji nazewnictwa.

### Struktura polityk

Polityki są przechowywane w katalogu `app/Policies` i powiązane z modelami. Na przykład, `UserPolicy` jest powiązana z modelem `User`.

### Rejestracja polityk

Polityki są automatycznie rejestrowane przez Laravel przy użyciu konwencji nazewnictwa. Można też ręcznie zarejestrować polityki w pliku `app/Providers/AuthServiceProvider.php`:

```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    'App\Models\User' => 'App\Policies\UserPolicy',
    'App\Models\Project' => 'App\Policies\ProjectPolicy',
    'App\Models\Investment' => 'App\Policies\InvestmentPolicy',
];
```

### Przykład polityki użytkownika

```php
// app/Policies/UserPolicy.php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id || $user->isAdmin();
    }
}
```

### Przykład polityki inwestycji (z obsługą subskrypcji)

```php
// app/Policies/InvestmentPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Investment;
use App\Models\Project;

class InvestmentPolicy
{
    public function create(User $user, Project $project): bool
    {
        // Użytkownik musi mieć zweryfikowane KYC i aktywną subskrypcję
        return $user->hasVerifiedKyc() && $user->hasActiveSubscription();
    }

    public function view(User $user, Investment $investment): bool
    {
        // Właściciel inwestycji lub administrator może zobaczyć
        if ($user->id === $investment->user_id || $user->isAdmin()) {
            return true;
        }
        
        // Właściciel projektu powiązanego z inwestycją również może zobaczyć
        return $user->canManageProjects() && 
               $user->id === $investment->project->user_id;
    }
}
```

### Przykład polityki projektu (z obsługą subskrypcji)

```php
// app/Policies/ProjectPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        // Każdy zweryfikowany użytkownik może przeglądać listę projektów
        return $user->hasVerifiedKyc();
    }

    public function view(User $user, Project $project): bool
    {
        // Właściciel projektu, administrator lub użytkownik z subskrypcją
        return $user->isAdmin() || 
               $user->id === $project->user_id || 
               $user->hasActiveSubscription();
    }

    public function create(User $user): bool
    {
        // Tylko użytkownicy z planem O-Premium mogą tworzyć projekty
        return $user->canManageProjects();
    }

    public function update(User $user, Project $project): bool
    {
        // Właściciel projektu lub administrator
        return $user->isAdmin() || $user->id === $project->user_id;
    }

    public function delete(User $user, Project $project): bool
    {
        // Właściciel projektu lub administrator
        return $user->isAdmin() || $user->id === $project->user_id;
    }
}
```

## Kiedy używać middleware, a kiedy polityk

### Middleware
- Używaj dla prostych, ogólnych reguł
- Idealnie do kontroli dostępu na poziomie grup tras
- Dobre dla warunków, które dotyczą użytkownika, a nie konkretnych zasobów

```php
// routes/web.php
Route::middleware(['auth', 'verified.kyc', 'subscription.plan:owner'])->group(function () {
    Route::resource('my-projects', ProjectController::class);
});
```

### Polityki
- Używaj dla złożonych reguł na poziomie zasobu
- Dobre gdy logika autoryzacji zależy od atrybutów zasobu
- Idealne do kontroli CRUD dla modeli

```php
// app/Http/Controllers/ProjectController.php
public function update(Request $request, Project $project)
{
    $this->authorize('update', $project);
    // Reszta logiki...
}
```

## Integracja z modelem opartym na subskrypcjach

Nowe podejście oparte na subskrypcjach wymaga następujących zmian:

1. **W modelu User** dodajemy metody pomocnicze do sprawdzania dostępu:

```php
// app/Models/User.php
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

// Zachowanie kompatybilności z istniejącymi metodami:
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

2. **W trasach** używamy nowego middleware:

```php
// routes/web.php
// Trasy dla właścicieli projektów
Route::middleware(['auth', 'verified.kyc', 'subscription.plan:owner'])->group(function () {
    Route::resource('my-projects', ProjectController::class);
});

// Trasy dla inwestorów premium
Route::middleware(['auth', 'verified.kyc', 'subscription.plan:premium'])->group(function () {
    Route::get('/exclusive-projects', [ProjectController::class, 'exclusive']);
});
```

3. **W politykach** wykorzystujemy nowe metody pomocnicze:

```php
// app/Policies/ProjectPolicy.php
public function create(User $user): bool
{
    return $user->canManageProjects();
}
```

## Best Practices dla autoryzacji

1. **Używaj middleware dla globalnych reguł** - weryfikacja KYC, subskrypcji itp.
2. **Grupuj logikę autoryzacji w metodach modelu** - `canManageProjects()`, `hasFullAccess()`
3. **Warstw autoryzację** - najpierw middleware ogólne, potem szczegółowe polityki
4. **Testuj reguły autoryzacji** - pisz testy sprawdzające zarówno pozytywne jak i negatywne przypadki
5. **Zachowaj spójność** - używaj tych samych mechanizmów w całej aplikacji
6. **Stosuj przekierowania z informacją zwrotną** - użytkownik powinien wiedzieć, dlaczego nie ma dostępu
7. **Unikaj duplikowania logiki** - centralizuj reguły w jednym miejscu

## Autoryzacja w komponentach Livewire

W komponentach Livewire, możemy stosować polityki w następujący sposób:

```php
// app/Http/Livewire/Projects/ProjectsList.php
namespace App\Http\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;

class ProjectsList extends Component
{
    public function mount()
    {
        // Sprawdź, czy użytkownik może oglądać projekty
        $this->authorize('viewAny', Project::class);
    }
    
    public function deleteProject(Project $project)
    {
        // Sprawdź, czy użytkownik może usunąć projekt
        $this->authorize('delete', $project);
        
        $project->delete();
    }
    
    // Reszta komponentu...
}
```

## Podsumowanie

W InvestClub stosujemy zarówno middleware jak i polityki, aby zarządzać autoryzacją. Middleware odpowiada za proste, globalne reguły, a polityki za szczegółowe reguły na poziomie zasobów. Nowy model oparty na subskrypcjach jest zintegrowany z obydwoma mechanizmami poprzez nowe middleware `subscription.plan` oraz odpowiednie metody w modelu `User`, zachowując kompatybilność z istniejącymi rozwiązaniami. 