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
    Route::get('/subscription', [StripeController::class, 'showSubscription'])->name('subscription');
    Route::post('/subscription/checkout', [StripeController::class, 'createCheckoutSession'])->name('subscription.checkout');
    Route::get('/subscription/success', [StripeController::class, 'handleCheckoutSuccess'])->name('subscription.success');
    Route::post('/subscription/cancel', [StripeController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/billing-portal', [StripeController::class, 'billingPortal'])->name('billing.portal');
    Route::get('/kyc/verify', [StripeController::class, 'showKycStatus'])->name('kyc.verify');
    Route::post('/kyc/verify', [StripeController::class, 'startKycVerification'])->name('kyc.start');
    Route::get('/kyc/completed', [StripeController::class, 'kycCompleted'])->name('kyc.completed');
});

// Trasa webhooka dla Stripe
Route::post('/stripe/webhook', [StripeController::class, 'handleKycWebhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Trasa webhooka dla subskrypcji
Route::post('/stripe/webhook/subscription', [StripeController::class, 'handleSubscriptionWebhook'])
    ->name('webhook.subscription')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
