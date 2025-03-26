<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Admin\ProjectVerificationController;
use App\Http\Controllers\TestLocaleController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Trasa do zmiany języka dostępna dla wszystkich
Route::get('/language/{locale}', function ($locale) {
    // Dodajemy rozszerzone logowanie
    Log::info('====== ROUTE LANGUAGE SWITCH START ======');
    Log::info('Żądana zmiana języka na: ' . $locale);
    Log::info('Obecny język App::getLocale(): ' . App::getLocale());
    Log::info('Obecny język w sesji: ' . session()->get('locale', 'brak'));
    Log::info('Session ID: ' . session()->getId());
    Log::info('Dostępne języki: ' . implode(', ', config('app.available_locales', ['en', 'pl', 'de'])));
    
    // Sprawdź czy język jest obsługiwany
    if (in_array($locale, config('app.available_locales', ['en', 'pl', 'de']))) {
        // Ustaw język w sesji
        session()->put('locale', $locale);
        
        // Ustaw język aplikacji
        app()->setLocale($locale);
        
        Log::info('Język został zmieniony na: ' . $locale);
        Log::info('App::getLocale() po zmianie: ' . App::getLocale());
        Log::info('Język w sesji po zmianie: ' . session()->get('locale', 'brak'));
    } else {
        Log::warning('Próba ustawienia nieobsługiwanego języka: ' . $locale);
    }
    
    Log::info('Poprzedni URL: ' . url()->previous());
    Log::info('Obecny URL: ' . url()->current());
    
    // Przekieruj do poprzedniej strony
    $redirectUrl = url()->previous() == url()->current() ? '/' : url()->previous();
    Log::info('Przekierowuję do: ' . $redirectUrl);
    Log::info('====== ROUTE LANGUAGE SWITCH END ======');
    
    if (url()->previous() == url()->current()) {
        return redirect('/');
    }
    
    return redirect()->back();
})->name('language.switch');

// Nowe trasy dla logowania i rejestracji z naszym layoutem
Route::get('/login', function () {
    return view('auth.front-login');
})->middleware('guest')->name('login');

Route::get('/register', function () {
    return view('auth.front-register');
})->middleware('guest')->name('register');

// Dodajemy tymczasową trasę bez middleware do debugowania
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->get('/projects/create-debug', [ProjectController::class, 'create'])->name('projects.create.debug');

// Dodajemy jawną trasę dla tworzenia projektów, aby naprawić problem z dostępem
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'verified.kyc'])->get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Trasy dla weryfikacji projektów przez administratora
    Route::middleware(['role:Administrator'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/projects/verification', [ProjectVerificationController::class, 'index'])->name('projects.verification');
        Route::patch('/projects/{project}/accept', [ProjectVerificationController::class, 'accept'])->name('projects.accept');
        Route::patch('/projects/{project}/reject', [ProjectVerificationController::class, 'reject'])->name('projects.reject');
    });

    // Role-specific dashboards
    Route::get('/admin/dashboard', function() {
        return view('admin.dashboard');
    })->middleware('role:admin')->name('admin.dashboard');
    
    Route::get('/manager/dashboard', function() {
        return view('manager.dashboard');
    })->middleware('role:manager')->name('manager.dashboard');

    // Trasy dla użytkowników (tylko dla administratorów)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // Trasy dla projektów - wymagają weryfikacji KYC i aktywnej subskrypcji
    Route::middleware(['verified.kyc', 'active.subscription'])->group(function () {
        // Podstawowe projekty dostępne dla wszystkich użytkowników z aktywną subskrypcją
        Route::resource('projects', ProjectController::class, ['only' => ['index', 'show']]);
        
        // Trasy dla inwestycji - tylko dla tworzenia nowych inwestycji (wymaga subskrypcji)
        Route::resource('investments', InvestmentController::class, ['only' => ['create', 'store', 'index', 'edit', 'update', 'destroy']]);
    });
    
    // Trasy dla inwestycji - dostępne dla wszystkich zalogowanych użytkowników (właścicieli projektów i inwestorów)
    Route::get('investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');
    Route::patch('investments/{investment}/change-status', [InvestmentController::class, 'changeStatus'])->name('investments.changeStatus');
    
    // Trasy dla właścicieli projektów z planem O-Premium
    Route::middleware(['verified.kyc', 'subscription.plan:owner'])->group(function () {
        // Jawne definicje tras dla projektów zamiast resource (bez create, które jest zdefiniowane powyżej)
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        
        // Inne trasy pozostają bez zmian
        Route::patch('/projects/{project}/change-status', [ProjectController::class, 'changeStatus'])
            ->name('projects.changeStatus');
        
        // Dashboard dla właścicieli projektów
        Route::get('/project-dashboard', [DashboardController::class, 'projectOwner'])
            ->name('project.dashboard');
    });
    
    // Trasy dla inwestorów premium
    Route::middleware(['verified.kyc', 'subscription.plan:premium'])->group(function () {
        // Ekskluzywne projekty
        Route::get('/exclusive-projects', [ProjectController::class, 'exclusive'])
            ->name('projects.exclusive');
    });
    
    // Trasy dotyczące Stripe
    Route::prefix('stripe')->group(function () {
        Route::get('/subscription', [StripeController::class, 'showSubscription'])->name('subscription');
        Route::post('/checkout', [StripeController::class, 'createCheckoutSession'])->name('stripe.checkout');
        Route::get('/success', [StripeController::class, 'handleCheckoutSuccess'])->name('subscription.success');
        Route::post('/cancel', [StripeController::class, 'cancelSubscription'])->name('subscription.cancel');
        Route::post('/update', [StripeController::class, 'updateSubscription'])->name('subscription.update');
        Route::post('/portal', [StripeController::class, 'createPortalSession'])->name('stripe.portal');
        Route::post('/update-portal', [StripeController::class, 'updateSubscriptionPortal'])->name('stripe.update-portal');
        Route::get('/update-subscription', [StripeController::class, 'updateSubscription'])->name('stripe.update-subscription');
        Route::post('/webhook', [StripeController::class, 'handleWebhook'])->name('stripe.webhook')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        Route::post('/webhook/kyc', [StripeController::class, 'handleKycWebhook'])->name('stripe.webhook.kyc')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    });

    // Trasy dla KYC
    Route::prefix('kyc')->group(function () {
        Route::get('/verify', [StripeController::class, 'showKycStatus'])->name('kyc.verify');
        Route::post('/verify', [StripeController::class, 'startKycVerification'])->name('kyc.start');
        Route::get('/completed', [StripeController::class, 'kycCompleted'])->name('kyc.completed');
    });
});

// Trasy testowe dla lokalizacji
Route::get('/test/locale', [TestLocaleController::class, 'index'])->name('test.locale');
Route::get('/test/locale/{locale}', [TestLocaleController::class, 'setLocale'])->name('test.locale.set');
