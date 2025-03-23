<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $plan
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $plan): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        Log::info('Sprawdzanie planu subskrypcji dla użytkownika: ' . $user->id . ', wymagany plan: ' . $plan);
        
        // Sprawdź czy użytkownik ma aktywną subskrypcję
        if ($user->stripe_subscription_status !== 'active') {
            Log::warning('Użytkownik nie ma aktywnej subskrypcji: ' . $user->id);
            return redirect()->route('subscription')
                ->with('warning', 'Aby uzyskać dostęp do tej funkcji, potrzebujesz aktywnej subskrypcji.');
        }
        
        // Sprawdź czy użytkownik ma odpowiedni plan
        if ($plan === 'owner' && $user->plan_type !== 'premium-owner') {
            Log::warning('Użytkownik nie ma planu właściciela: ' . $user->id);
            return redirect()->route('subscription')
                ->with('warning', 'Ta funkcja wymaga planu O-Premium dla właścicieli projektów.');
        }
        
        if ($plan === 'premium' && !in_array($user->plan_type, ['premium-investor', 'premium-owner'])) {
            Log::warning('Użytkownik nie ma planu premium: ' . $user->id);
            return redirect()->route('subscription')
                ->with('warning', 'Ta funkcja wymaga planu premium.');
        }
        
        return $next($request);
    }
    
    /**
     * Sprawdza, czy użytkownik może zarządzać projektami.
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    private function canManageProjects($user): bool
    {
        return $user->hasActiveSubscription() && 
               ($user->plan_type === 'premium-owner');
    }
}
