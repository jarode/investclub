<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;

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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        
        // Trasy dla inwestycji - podstawowe operacje dostępne dla wszystkich użytkowników
        Route::resource('investments', InvestmentController::class, ['only' => ['index', 'show']]);
    });
    
    // Trasy dla właścicieli projektów z planem O-Premium
    Route::middleware(['verified.kyc', 'subscription.plan:owner'])->group(function () {
        // Zarządzanie projektami
        Route::resource('projects', ProjectController::class, ['except' => ['index', 'show']]);
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
        
        // Zaawansowane operacje inwestycyjne
        Route::resource('investments', InvestmentController::class, ['except' => ['index', 'show']]);
        Route::patch('/investments/{investment}/change-status', [InvestmentController::class, 'changeStatus'])
            ->name('investments.changeStatus');
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
    });

    // Trasy dla KYC
    Route::prefix('kyc')->group(function () {
        Route::get('/verify', [StripeController::class, 'showKycStatus'])->name('kyc.verify');
        Route::post('/verify', [StripeController::class, 'startKycVerification'])->name('kyc.start');
        Route::get('/completed', [StripeController::class, 'kycCompleted'])->name('kyc.completed');
    });
});
