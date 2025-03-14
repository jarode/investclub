# Middleware i Polityki w InvestClub

## Wprowadzenie

W aplikacji InvestClub wykorzystujemy dwa główne mechanizmy autoryzacji:

1. **Middleware** - do kontroli dostępu na poziomie tras (routes)
2. **Polityki (Policies)** - do kontroli dostępu na poziomie zasobów (resources)

Laravel Jetstream wykorzystuje zarówno middleware jak i polityki, ale główny nacisk kładzie na polityki do zarządzania autoryzacją w aplikacji.

## Middleware w Laravel 11

W Laravel 11 wprowadzono nowy sposób konfiguracji middleware poprzez klasę `Illuminate\Foundation\Configuration\Middleware`. Konfiguracja odbywa się w pliku `bootstrap/app.php`.

### Rejestracja Middleware

```php
// bootstrap/app.php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // ...
    ->withMiddleware(function (Middleware $middleware) {
        // Rejestracja middleware dla ról
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    // ...
    ->create();
```

### RoleMiddleware

Nasze middleware `RoleMiddleware` sprawdza, czy zalogowany użytkownik ma określoną rolę:

```php
// app/Http/Middleware/RoleMiddleware.php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (!auth()->user()->hasRole($role)) {
        abort(403, 'Brak dostępu - wymagana rola: ' . $role);
    }

    return $next($request);
}
```

### Użycie Middleware w Kontrolerach

Middleware można stosować na poziomie konstruktora kontrolera:

```php
// app/Http/Controllers/UserController.php
public function __construct()
{
    $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
}
```

## Polityki (Policies) w Laravel Jetstream

Jetstream opiera się głównie na politykach do kontroli dostępu do zasobów. Polityki są klasami PHP, które grupują logikę autoryzacji dla danego modelu.

### Struktura Polityk

Polityki są umieszczone w katalogu `app/Policies`. Każda polityka powinna być powiązana z modelem, którego dotyczy.

### Rejestracja Polityk

Laravel automatycznie wykrywa polityki na podstawie konwencji nazewnictwa. Na przykład, polityka dla modelu `User` powinna nazywać się `UserPolicy`.

### UserPolicy

```php
// app/Policies/UserPolicy.php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Metoda before uruchamiana przed wszystkimi innymi metodami
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true; // Administratorzy mają dostęp do wszystkiego
        }
        
        return null; // Kontynuuj sprawdzanie w pozostałych metodach
    }

    // Określa czy użytkownik może wyświetlać listę użytkowników
    public function viewAny(User $user): bool
    {
        return true; // Każdy zalogowany użytkownik może wyświetlać listę
    }

    // Określa czy użytkownik może wyświetlić profil innego użytkownika
    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasRole('manager');
    }

    // Inne metody polityki...
}
```

### InvestmentPolicy

```php
// app/Policies/InvestmentPolicy.php
namespace App\Policies;

use App\Models\User;

class InvestmentPolicy
{
    // Metody polityki dla inwestycji...
    
    public function invest(User $user, $investment): bool
    {
        return $user->isVerified(); // Tylko zweryfikowani użytkownicy mogą inwestować
    }
}
```

## Użycie Polityk w Kontrolerach

Polityki można stosować na kilka sposobów:

### Sposób 1: Metoda `authorize()`

```php
// W kontrolerze
public function show(User $user)
{
    $this->authorize('view', $user);
    
    return view('users.show', compact('user'));
}
```

### Sposób 2: Fasada Gate

```php
// W kontrolerze
use Illuminate\Support\Facades\Gate;

public function manageInvestments(User $user)
{
    if (Gate::denies('manageInvestments', $user)) {
        abort(403);
    }
    
    // Logika zarządzania inwestycjami
}
```

### Sposób 3: W Blade

```blade
@can('update', $user)
    <a href="{{ route('users.edit', $user) }}">Edytuj</a>
@endcan
```

## Middleware vs Polityki - Kiedy użyć?

### Middleware
- Do kontroli dostępu na poziomie tras
- Do prostych, globalnych reguł autoryzacji
- Przykład: Sprawdzanie czy użytkownik jest zalogowany, czy ma określoną rolę

### Polityki
- Do kontroli dostępu na poziomie zasobów
- Do bardziej złożonych reguł autoryzacji
- Przykład: Sprawdzanie czy użytkownik może edytować konkretny zasób

## Najlepsze Praktyki

1. **Używaj middleware** do prostych, globalnych reguł (np. czy użytkownik ma rolę)
2. **Używaj polityk** do bardziej szczegółowych reguł (np. czy użytkownik może edytować konkretny zasób)
3. **Używaj metody `before`** w politykach dla uprawnień administratora
4. **Grupuj logikę autoryzacji** w metodach modelu (np. `hasRole`, `isAdmin`, `isVerified`)
5. **Testuj reguły autoryzacji** za pomocą testów funkcjonalnych

## Autoryzacja w Livewire

Jeśli używasz komponentów Livewire (jak w Jetstream), możesz zastosować polityki w podobny sposób:

```php
// W komponencie Livewire
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserProfile extends Component
{
    use AuthorizesRequests;
    
    public function save()
    {
        $this->authorize('update', $this->user);
        
        // Zapisz zmiany
    }
}
```

## Podsumowanie

W InvestClub stosujemy zarówno middleware jak i polityki do zarządzania autoryzacją. Middleware używamy do prostych, globalnych reguł, a polityk do bardziej szczegółowych reguł na poziomie zasobów. Dzięki temu mamy pełną kontrolę nad dostępem do różnych części aplikacji. 