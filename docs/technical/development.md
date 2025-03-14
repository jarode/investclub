# Metodologia Rozwoju

## Struktura Projektu (Jetstream)

### 1. Komponenty Livewire
- Lokalizacja: `app/Http/Livewire/`
- Konwencja nazewnictwa: `PascalCase`
- Struktura:
  ```
  app/Http/Livewire/
  ├── Projects/
  │   ├── CreateProject.php
  │   ├── ListProjects.php
  │   └── ShowProject.php
  ├── Investors/
  │   └── ...
  └── Teams/
      └── ...
  ```

### 2. Modele i Relacje
- Lokalizacja: `app/Models/`
- Dziedziczenie z Jetstream:
  ```php
  use Laravel\Jetstream\HasTeams;
  use Laravel\Sanctum\HasApiTokens;
  ```

### 3. Polityki Dostępu
- Lokalizacja: `app/Policies/`
- Integracja z Jetstream Teams

### 4. API (Sanctum)
- Lokalizacja: `app/Http/Controllers/Api/`
- Autentykacja przez Sanctum
- Dokumentacja OpenAPI

## Test-Driven Development (TDD)

### 1. Struktura Testów
```
tests/
├── Feature/
│   ├── Projects/
│   │   ├── CreateProjectTest.php
│   │   ├── ListProjectsTest.php
│   │   └── ShowProjectTest.php
│   ├── Investors/
│   └── Teams/
├── Unit/
│   ├── Models/
│   └── Services/
└── Browser/
    └── Projects/
```

### 2. Konwencje Testowe

#### Testy Jednostkowe
```php
public function test_project_can_be_created()
{
    // Arrange
    $projectData = [...];

    // Act
    $project = Project::create($projectData);

    // Assert
    $this->assertInstanceOf(Project::class, $project);
}
```

#### Testy Funkcjonalne
```php
public function test_authenticated_user_can_create_project()
{
    // Arrange
    $user = User::factory()->create();
    $projectData = [...];

    // Act
    $response = $this->actingAs($user)
        ->post(route('projects.store'), $projectData);

    // Assert
    $response->assertRedirect(route('projects.show', 1));
    $this->assertDatabaseHas('projects', $projectData);
}
```

#### Testy Integracyjne
```php
public function test_project_creation_triggers_notifications()
{
    // Arrange
    Notification::fake();
    $user = User::factory()->create();
    
    // Act
    $project = Project::factory()->create();
    
    // Assert
    Notification::assertSentTo($user, NewProjectNotification::class);
}
```

### 3. Proces TDD

1. **Czerwony** - Napisz test
   ```bash
   php artisan make:test CreateProjectTest
   ```

2. **Zielony** - Implementuj funkcjonalność
   ```bash
   php artisan make:livewire Projects/CreateProject
   ```

3. **Refaktor** - Optymalizuj kod
   ```bash
   php artisan test --filter=CreateProjectTest
   ```

## Standardy Kodowania

### 1. Laravel Pint
```bash
# Sprawdź styl kodu
php artisan pint --test

# Napraw styl kodu
php artisan pint
```

### 2. Konwencje Jetstream
- Używaj Blade Components
- Stosuj Actions dla logiki biznesowej
- Wykorzystuj Events i Listeners

### 3. Dobre Praktyki
```php
// Actions
class CreateProject implements CreateProjectContract
{
    public function create(array $input): Project
    {
        return DB::transaction(fn () => 
            Project::create($input)
        );
    }
}

// Events
class ProjectCreated
{
    public function __construct(public Project $project) {}
}

// Listeners
class NotifyProjectStakeholders
{
    public function handle(ProjectCreated $event): void
    {
        // Implementacja
    }
}
```

## Workflow Developmentu

### 1. Przygotowanie
```bash
# Utwórz branch
git checkout -b feature/project-creation

# Utwórz testy
php artisan make:test Projects/CreateProjectTest
```

### 2. Implementacja
```bash
# Uruchom testy w trybie watch
php artisan test --watch

# Implementuj funkcjonalność
php artisan make:livewire Projects/CreateProject
```

### 3. Weryfikacja
```bash
# Uruchom wszystkie testy
php artisan test

# Sprawdź pokrycie kodu
php artisan test --coverage

# Sprawdź styl kodu
php artisan pint --test
```

### 4. Dokumentacja
- Aktualizuj PHPDoc
- Dodawaj komentarze do testów
- Aktualizuj README.md

## CI/CD Pipeline

### 1. GitHub Actions
```yaml
name: Tests
on: [push, pull_request]
jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run Tests
        run: |
          composer install
          php artisan test
          php artisan pint --test
```

### 2. Automatyzacja
- Testy przy każdym push
- Sprawdzanie stylu kodu
- Generowanie dokumentacji
- Deployment na staging

## Monitorowanie i Debugowanie

### 1. Laravel Telescope
- Monitorowanie requestów
- Debugowanie zapytań
- Śledzenie eventów

### 2. Laravel Horizon
- Zarządzanie kolejkami
- Monitorowanie jobów
- Analiza wydajności

### 3. Logowanie
```php
Log::channel('projects')->info('Project created', [
    'project_id' => $project->id,
    'user_id' => auth()->id()
]);
```

## Wymagania Techniczne dla Zgodności z Regulacjami

### Etap 1 - Marketplace + Private Club

#### 1. System KYC/AML
```php
// Integracja z Veriff
use App\Services\KYC\VeriffService;

class KYCVerification
{
    public function verify(User $user, array $documents): VerificationResult
    {
        // Weryfikacja dokumentów
        // Sprawdzenie list sankcyjnych
        // Zapisanie wyniku weryfikacji
    }
}
```

#### 2. Bezpieczeństwo Danych
```php
// Szyfrowanie wrażliwych danych
use Illuminate\Support\Facades\Crypt;

class DataEncryption
{
    public function encryptSensitiveData(array $data): array
    {
        // Szyfrowanie danych osobowych
        // Bezpieczne przechowywanie dokumentów
    }
}
```

#### 3. System Powiadomień i Raportowania
```php
// Monitorowanie aktywności
class ActivityMonitoring
{
    public function logActivity(string $type, array $data): void
    {
        // Logowanie aktywności użytkowników
        // Wykrywanie podejrzanych działań
    }
}
```

### Etap 2 - Przygotowanie do ECSP

#### 1. System Zarządzania Ryzykiem
```php
class RiskManagement
{
    public function assessProjectRisk(Project $project): RiskAssessment
    {
        // Ocena ryzyka projektu
        // Kategoryzacja ryzyka
        // Rekomendacje
    }
}
```

#### 2. Raportowanie Compliance
```php
class ComplianceReporting
{
    public function generateReport(string $type, DatePeriod $period): Report
    {
        // Generowanie raportów zgodności
        // Statystyki i analizy
    }
}
```

### Etap 3 - Pełna Licencja ECSP

#### 1. System Transakcyjny
```php
class TransactionSystem
{
    public function processInvestment(Investment $investment): Transaction
    {
        // Przetwarzanie transakcji
        // Escrow
        // Rozliczenia
    }
}
```

#### 2. Monitoring Transakcji
```php
class TransactionMonitoring
{
    public function monitorTransaction(Transaction $transaction): void
    {
        // Monitoring transakcji
        // Wykrywanie nieprawidłowości
        // Raportowanie do regulatora
    }
}
```

## Komponenty Techniczne MVP

### 1. System Profilowania
```php
class InvestorProfile extends Model
{
    use HasFactory, HasPreferences;

    protected $casts = [
        'investment_range' => AsMoneyRange::class,
        'preferences' => AsCollection::class,
        'expertise' => AsArray::class
    ];

    public function matchingScore(Project $project): float
    {
        // Algorytm scoringu dopasowania
        return $this->preferences->matchProject($project);
    }
}

class ProjectProfile extends Model
{
    use HasFactory, HasMetrics;

    protected $casts = [
        'financial_metrics' => AsMetricsCollection::class,
        'team' => AsTeamCollection::class,
        'documents' => AsDocumentCollection::class
    ];

    public function calculateScore(): float
    {
        // Scoring projektu bazujący na metryczkach
        return $this->metrics->calculateScore();
    }
}
```

### 2. System Matchingu
```php
class MatchingService
{
    public function findMatchingProjects(Investor $investor): Collection
    {
        return Project::query()
            ->with(['profile', 'metrics'])
            ->whereMatchesPreferences($investor->preferences)
            ->orderByMatchingScore($investor)
            ->get();
    }

    public function notifyAboutMatches(): void
    {
        // Powiadomienia o nowych dopasowaniach
        // Alerty i rekomendacje
    }
}
```

### 3. Komunikacja
```php
class SecureMessaging
{
    use EncryptsMessages;

    public function sendMessage(User $from, User $to, string $message): Message
    {
        return DB::transaction(function () use ($from, $to, $message) {
            // Szyfrowanie i wysyłka wiadomości
            // Zapisywanie historii
            // Powiadomienia
        });
    }
}

class VirtualMeetingRoom
{
    public function schedule(Project $project, array $participants): Meeting
    {
        // Tworzenie pokoju spotkań
        // Generowanie linków dostępowych
        // Kalendarz i przypomnienia
    }
}
```

### 4. Analityka
```php
class Analytics
{
    public function generateInvestorDashboard(Investor $investor): Dashboard
    {
        return new Dashboard([
            'matching_projects' => $this->getMatchingProjects($investor),
            'activity_stats' => $this->getActivityStats($investor),
            'market_trends' => $this->getMarketTrends()
        ]);
    }

    public function exportData(string $type, DateRange $period): Export
    {
        // Eksport danych w różnych formatach
        // Generowanie raportów
    }
}
```

### 5. Bezpieczeństwo
```php
class SecurityService
{
    public function setup2FA(User $user): void
    {
        // Konfiguracja 2FA
        // Generowanie kodów backup
    }

    public function auditActivity(string $type, array $data): void
    {
        // Logowanie aktywności
        // Wykrywanie anomalii
        // Alerty bezpieczeństwa
    }
}
```

### 6. System Wiedzy
```php
class KnowledgeBase
{
    public function publishArticle(Article $article): void
    {
        // Publikacja artykułu
        // Kategoryzacja
        // Powiadomienia subskrybentów
    }

    public function scheduleWebinar(Webinar $webinar): void
    {
        // Planowanie webinaru
        // Rejestracja uczestników
        // Przypomnienia
    }
}
``` 