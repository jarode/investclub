# Przewodnik implementacyjny InvestClub

## Model biznesowy

InvestClub działa w modelu **Marketplace + Private Club**, co oznacza:

1. Platforma jest katalogiem projektów inwestycyjnych (marketplace), ale nie pośredniczy w transakcjach
2. Dostęp do platformy mają tylko zweryfikowani użytkownicy (private club)
3. Transakcje odbywają się poza platformą, po bezpośrednim kontakcie inwestora z właścicielem projektu
4. Platforma zarabia na subskrypcjach, nie na prowizjach od transakcji

## Kluczowe elementy implementacji

### 1. System użytkowników

#### Role użytkowników
- **Administrator** (`admin`): zarządza platformą, weryfikuje użytkowników i projekty
- **Manager** (`manager`): tworzy i zarządza projektami, kontaktuje się z inwestorami
- **Inwestor** (`investor`): przegląda projekty, wyraża zainteresowanie

#### Weryfikacja KYC
- Każdy użytkownik przechodzi weryfikację KYC (Know Your Customer)
- Status KYC (`kyc_status`): `pending`, `in_progress`, `verified`, `rejected`
- Tylko użytkownicy ze statusem `verified` mogą korzystać z pełnej funkcjonalności

### 2. System projektów

#### Struktura projektu
- Podstawowe informacje: nazwa, opis, kategoria, lokalizacja
- Dane finansowe: kwota docelowa, minimalna inwestycja
- Harmonogram: data rozpoczęcia, data zakończenia
- Informacje dodatkowe: prognozy zwrotu, poziom ryzyka

#### Statusy projektu
- `draft`: projekt w trakcie tworzenia, widoczny tylko dla właściciela i administratorów
- `active`: projekt aktywny, widoczny dla wszystkich zweryfikowanych użytkowników
- `completed`: projekt zakończony, archiwum
- `cancelled`: projekt anulowany

### 3. System inwestycji (deklaracji zainteresowania)

#### Struktura inwestycji
- Relacje: inwestor, projekt
- Kwota: deklarowana potencjalna inwestycja
- Kontakt: preferencje kontaktu, dane kontaktowe
- Notatki: dodatkowe informacje od inwestora

#### Statusy inwestycji
- `interested`: inwestor wyraził wstępne zainteresowanie
- `in_talks`: trwają rozmowy między inwestorem a managerem projektu
- `contract_signed`: umowa została podpisana (poza platformą)
- `cancelled`: inwestor lub manager anulował proces

### 4. System powiadomień

#### Typy powiadomień
- Systemowe: aktualizacje platformy, zmiany statusu KYC
- Projektowe: nowe projekty, zmiany w projektach
- Inwestycyjne: zmiany statusu inwestycji, wiadomości od managerów

## Implementacja techniczna

### Migracje i modele

#### User
```php
// Dodatkowe pola w tabeli users
Schema::table('users', function (Blueprint $table) {
    $table->string('role')->default('investor');
    $table->string('kyc_status')->default('pending');
    $table->timestamp('kyc_verified_at')->nullable();
});

// Model User
class User extends Authenticatable
{
    // ...
    
    protected $fillable = [
        'name', 'email', 'password', 'role', 'kyc_status'
    ];
    
    // Relacje
    public function ownedProjects() {
        return $this->hasMany(Project::class, 'owner_id');
    }
    
    public function investments() {
        return $this->hasMany(Investment::class);
    }
    
    // Pomocnicze metody
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    public function isManager() {
        return $this->role === 'manager';
    }
    
    public function isKycVerified() {
        return $this->kyc_status === 'verified';
    }
}
```

#### Project
```php
// Tabela projects
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
    $table->string('name');
    $table->text('description');
    $table->decimal('target_amount', 12, 2);
    $table->decimal('min_investment', 12, 2);
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('returns_projection', 5, 2)->nullable();
    $table->string('risk_level')->nullable();
    $table->string('category');
    $table->string('location');
    $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
    $table->timestamps();
    $table->softDeletes();
});

// Model Project
class Project extends Model
{
    // ...
    
    protected $fillable = [
        'owner_id', 'name', 'description', 'target_amount', 'min_investment',
        'start_date', 'end_date', 'returns_projection', 'risk_level',
        'category', 'location', 'status'
    ];
    
    // Relacje
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    public function investments() {
        return $this->hasMany(Investment::class);
    }
    
    // Pomocnicze metody
    public function isDraft() {
        return $this->status === 'draft';
    }
    
    public function isActive() {
        return $this->status === 'active';
    }
}
```

#### Investment
```php
// Tabela investments
Schema::create('investments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->enum('status', ['interested', 'in_talks', 'contract_signed', 'cancelled'])->default('interested');
    $table->string('contact_preference')->nullable();
    $table->text('contact_details')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

// Model Investment
class Investment extends Model
{
    // ...
    
    protected $fillable = [
        'user_id', 'project_id', 'amount', 'status',
        'contact_preference', 'contact_details', 'notes'
    ];
    
    // Statusy
    const STATUS_INTERESTED = 'interested';
    const STATUS_IN_TALKS = 'in_talks';
    const STATUS_CONTRACT_SIGNED = 'contract_signed';
    const STATUS_CANCELLED = 'cancelled';
    
    // Relacje
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function project() {
        return $this->belongsTo(Project::class);
    }
    
    // Pomocnicze metody
    public function isInterested() {
        return $this->status === self::STATUS_INTERESTED;
    }
    
    public function isInTalks() {
        return $this->status === self::STATUS_IN_TALKS;
    }
}
```

### Kontrolery

#### ProjectController
```php
class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }
    
    public function index()
    {
        $projects = Project::when(auth()->user()->isInvestor(), function($query) {
            return $query->where('status', 'active');
        })->get();
        
        return view('projects.index', compact('projects'));
    }
    
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create(array_merge(
            $request->validated(),
            ['owner_id' => auth()->id()]
        ));
        
        return redirect()->route('projects.show', $project);
    }
    
    public function changeStatus(Project $project, Request $request)
    {
        $this->authorize('changeStatus', $project);
        
        $request->validate([
            'status' => 'required|in:draft,active,completed,cancelled'
        ]);
        
        $project->update(['status' => $request->status]);
        
        return redirect()->route('projects.show', $project);
    }
}
```

#### InvestmentController
```php
class InvestmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Investment::class, 'investment');
    }
    
    public function store(StoreInvestmentRequest $request)
    {
        $project = Project::findOrFail($request->project_id);
        
        if ($project->status !== 'active') {
            return back()->with('error', 'Można deklarować zainteresowanie tylko aktywnymi projektami.');
        }
        
        if ($request->amount < $project->min_investment) {
            return back()->with('error', 'Kwota jest poniżej minimalnej wymaganej inwestycji.');
        }
        
        $investment = Investment::create([
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'amount' => $request->amount,
            'status' => Investment::STATUS_INTERESTED,
            'contact_preference' => $request->contact_preference,
            'contact_details' => $request->contact_details,
            'notes' => $request->notes
        ]);
        
        return redirect()->route('investments.show', $investment);
    }
    
    public function update(Investment $investment, UpdateInvestmentRequest $request)
    {
        $investment->update($request->validated());
        
        return redirect()->route('investments.show', $investment);
    }
}
```

### Polityki dostępu

#### ProjectPolicy
```php
class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }
    
    public function view(User $user, Project $project): bool
    {
        if (in_array($project->status, ['active', 'completed'])) {
            return $user->isKycVerified();
        }
        
        return $project->owner_id === $user->id || 
               $user->role === 'admin' || 
               $user->role === 'manager';
    }
    
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']) && $user->isKycVerified();
    }
    
    public function update(User $user, Project $project): bool
    {
        if ($project->status === 'completed') {
            return false;
        }
        
        return $project->owner_id === $user->id || 
               $user->role === 'admin' || 
               $user->role === 'manager';
    }
    
    public function changeStatus(User $user, Project $project): bool
    {
        return $user->role === 'admin';
    }
}
```

#### InvestmentPolicy
```php
class InvestmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }
    
    public function view(User $user, Investment $investment): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        return $user->id === $investment->user_id;
    }
    
    public function create(User $user): bool
    {
        return $user->isKycVerified();
    }
    
    public function update(User $user, Investment $investment): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        return $user->id === $investment->user_id && $investment->isInterested();
    }
    
    public function delete(User $user, Investment $investment): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        return $user->id === $investment->user_id && 
               ($investment->isInterested() || $investment->isInTalks());
    }
}
```

## Testowanie

### Testy jednostkowe i integracyjne

- Testy modeli (relacje, metody pomocnicze)
- Testy kontrolerów (CRUD, walidacja)
- Testy polityk (autoryzacja)

### Testy funkcjonalne

- Test pełnego cyklu życia projektu
- Test procesu inwestycyjnego
- Test zarządzania statusami

## Wdrożenie

### Środowiska

- Development: dla bieżących prac rozwojowych
- Staging: do testowania przed wdrożeniem
- Production: środowisko produkcyjne

### CI/CD

- GitHub Actions do automatyzacji testów i wdrożeń
- Laravel Cloud do hostingu aplikacji

## Dalszy rozwój

- Rozbudowa panelu administracyjnego
- Zaawansowane algorytmy matchingu
- Integracja z narzędziami analitycznymi
- Optymalizacja wydajności dla dużej liczby użytkowników 