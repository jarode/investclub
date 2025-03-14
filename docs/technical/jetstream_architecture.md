# Architektura Oparta na Jetstream

## 1. Struktura Podstawowa

### Teams i Uprawnienia
```php
// app/Models/Team.php
class Team extends JetstreamTeam
{
    use HasFactory;
    
    const ROLE_ADMIN = 'admin';
    const ROLE_INVESTOR = 'investor';
    const ROLE_PROJECT_OWNER = 'project_owner';

    protected $fillable = [
        'name',
        'personal_team',
        'type', // investment_team, project_team
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}

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

### Rozszerzenie User
```php
// app/Models/User.php
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_id',
        'kyc_status',
        'kyc_verified_at',
        'preferred_language',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'investment_preferences' => 'array',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}
```

## 2. Komponenty Livewire

### Project Management
```php
// app/Http/Livewire/Projects/CreateProject.php
class CreateProject extends Component
{
    use WithFileUploads;

    public $name;
    public $description;
    public $investment_amount;
    public $documents = [];
    public $team_id;

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'required',
        'investment_amount' => 'required|numeric|min:1000',
        'documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
    ];

    public function mount()
    {
        $this->team_id = auth()->user()->currentTeam->id;
    }

    public function create()
    {
        $this->validate();

        $project = Project::create([
            'name' => $this->name,
            'description' => $this->description,
            'investment_amount' => $this->investment_amount,
            'team_id' => $this->team_id,
            'status' => 'draft'
        ]);

        foreach ($this->documents as $document) {
            $project->addMedia($document)
                ->toMediaCollection('documents');
        }

        $this->emit('projectCreated');
        
        return redirect()->route('projects.show', $project);
    }
}
```

### Project List
```php
// app/Http/Livewire/Projects/ProjectList.php
class ProjectList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function render()
    {
        return view('livewire.projects.list', [
            'projects' => Project::query()
                ->search($this->search)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10)
        ]);
    }
}
```

### Investment Dashboard
```php
// app/Http/Livewire/Dashboard/InvestorDashboard.php
class InvestorDashboard extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.dashboard.investor', [
            'matchingProjects' => $this->getMatchingProjects(),
            'activeInvestments' => $this->getActiveInvestments(),
            'statistics' => $this->getStatistics(),
        ]);
    }

    protected function getMatchingProjects()
    {
        return Project::query()
            ->with(['team', 'media'])
            ->whereMatchesPreferences(auth()->user()->investment_preferences)
            ->latest()
            ->paginate(10);
    }
}
```

## 3. Integracje

### Stripe Integration
```php
// app/Services/StripeService.php
class StripeService
{
    public function createCustomer(User $user): StripeCustomer
    {
        if ($user->stripe_id) {
            return $this->getCustomer($user->stripe_id);
        }

        $customer = \Stripe\Customer::create([
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
    
    public function startVerification(User $user, string $returnUrl)
    {
        $session = \Stripe\Identity\VerificationSession::create([
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
        
        return $session;
    }
    
    public function getVerificationStatus(string $sessionId)
    {
        return \Stripe\Identity\VerificationSession::retrieve($sessionId);
    }
}

// app/Http/Controllers/StripeWebhookController.php
class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, config('stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            abort(400);
        }

        switch ($event->type) {
            case 'identity.verification_session.verified':
                $this->handleVerificationSuccess($event->data->object);
                break;
                
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdate($event->data->object);
                break;
        }

        return response()->json(['status' => 'success']);
    }
    
    protected function handleVerificationSuccess($session)
    {
        $user = User::where('stripe_id', $session->metadata->user_id)->first();
        
        if ($user && $session->status === 'verified') {
            $user->kyc_status = 'verified';
            $user->kyc_verified_at = now();
            $user->save();
            
            event(new UserVerified($user));
        }
    }
}
```

### Wielojęzyczność
```php
// app/Http/Middleware/SetLocale.php
class SetLocale
{
    public function handle($request, Closure $next)
    {
        if ($user = auth()->user()) {
            app()->setLocale($user->preferred_language ?? config('app.locale'));
        } elseif ($locale = session('locale')) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}

// routes/web.php
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'de', 'pl'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');
```

## 4. Optymalizacja dla Laravel Cloud

### Queue Jobs
```php
// app/Jobs/ProcessProjectVerification.php
class ProcessProjectVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $project;
    
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function handle()
    {
        Redis::throttle('project-verifications')
            ->allow(30)
            ->every(60)
            ->then(function () {
                // Logika weryfikacji projektu
                $this->project->verify();
            }, function () {
                // Nie udało się uzyskać blokady
                return $this->release(30);
            });
    }
}
```

### Cache
```php
// app/Services/ProjectMatchingService.php
class ProjectMatchingService
{
    public function getMatchingProjects(User $user)
    {
        $cacheKey = "matching_projects:{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return Project::matching($user->investment_preferences)->get();
        });
    }
}
```

### Storage
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

## 5. Testy

### Feature Tests
```php
// tests/Feature/Projects/CreateProjectTest.php
class CreateProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_create_project()
    {
        $user = User::factory()->create(['kyc_verified_at' => now()]);
        $team = Team::factory()->create(['user_id' => $user->id]);
        $user->switchTeam($team);
        
        $response = $this->actingAs($user)
            ->livewire(CreateProject::class)
            ->set('name', 'Test Project')
            ->set('investment_amount', 100000)
            ->call('create');

        $this->assertTrue(Project::where('name', 'Test Project')->exists());
    }
    
    public function test_unverified_user_cannot_create_project()
    {
        $user = User::factory()->create(['kyc_verified_at' => null]);
        $team = Team::factory()->create(['user_id' => $user->id]);
        $user->switchTeam($team);
        
        $response = $this->actingAs($user)
            ->get(route('projects.create'));
            
        $response->assertRedirect(route('verification.notice'));
    }
}
```

### Unit Tests
```php
// tests/Unit/StripeServiceTest.php
class StripeServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->stripeMock = $this->mock(\Stripe\StripeClient::class);
        $this->service = new StripeService($this->stripeMock);
    }
    
    public function test_it_creates_customer_in_stripe()
    {
        $user = User::factory()->create();
        
        $this->stripeMock->customers->shouldReceive('create')
            ->once()
            ->with([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                    'team_id' => null
                ]
            ])
            ->andReturn((object) ['id' => 'cus_123456']);
            
        $customer = $this->service->createCustomer($user);
        
        $this->assertEquals('cus_123456', $customer->id);
        $this->assertEquals('cus_123456', $user->fresh()->stripe_id);
    }
}
``` 