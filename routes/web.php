<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InvestmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard dla wszystkich zalogowanych użytkowników
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Trasy chronione middleware 'role' - sprawdzające role użytkowników
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('role:admin')->name('admin.dashboard');
    
    Route::get('/manager/dashboard', function () {
        return view('manager.dashboard');
    })->middleware('role:manager')->name('manager.dashboard');
    
    // Trasy zarządzania użytkownikami - wykorzystujące policies
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // Trasy wymagające roli admin - muszą być przed trasą z parametrem {user}
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    
    // Trasy z parametrem {user}
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    
    // Trasy wymagające roli admin dla operacji z parametrem {user}
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Trasy dla projektów inwestycyjnych
    Route::resource('projects', ProjectController::class);
    
    // Dodatkowa trasa dla zmiany statusu projektu
    Route::patch('/projects/{project}/change-status', [ProjectController::class, 'changeStatus'])
        ->name('projects.changeStatus');
        
    // Trasy dla inwestycji
    Route::resource('investments', InvestmentController::class);
    
    // Dodatkowa trasa dla zmiany statusu inwestycji
    Route::patch('/investments/{investment}/change-status', [InvestmentController::class, 'changeStatus'])
        ->name('investments.changeStatus');
});
