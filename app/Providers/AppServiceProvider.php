<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Stripe\StripeClient::class, function ($app) {
            return new \Stripe\StripeClient(config('stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Logowanie zapytań SQL
        if (app()->environment('local')) {
            \Illuminate\Support\Facades\DB::listen(function($query) {
                $sql = $query->sql;
                $bindings = $query->bindings;
                $time = $query->time;
                
                // Wykluczamy niektóre zapytania, aby nie zaśmiecać logów
                if (strpos($sql, 'migrations') === false && strpos($sql, 'sessions') === false) {
                    \Illuminate\Support\Facades\Log::debug('Query', [
                        'sql' => $sql,
                        'bindings' => $bindings,
                        'time' => $time
                    ]);
                }
            });
        }
    }
}
