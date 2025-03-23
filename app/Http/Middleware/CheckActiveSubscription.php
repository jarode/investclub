<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        Log::info('Sprawdzanie aktywnej subskrypcji dla użytkownika: ' . $user->id);
        
        // Sprawdź czy użytkownik ma aktywną subskrypcję
        if ($user->stripe_subscription_status !== 'active') {
            Log::warning('Użytkownik nie ma aktywnej subskrypcji: ' . $user->id);
            return redirect()->route('subscription')
                ->with('warning', 'Aby uzyskać dostęp do tej funkcji, potrzebujesz aktywnej subskrypcji.');
        }
        
        return $next($request);
    }
} 