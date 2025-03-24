<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Admin\ProjectVerificationController;

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
});

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
