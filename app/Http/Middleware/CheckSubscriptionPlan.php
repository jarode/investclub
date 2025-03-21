<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $planType): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Administratorzy mają dostęp do wszystkiego
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Dla właścicieli projektów
        if ($planType === 'owner' && !$this->canManageProjects($user)) {
            return redirect()->route('subscription')
                ->with('warning', 'Ta funkcja wymaga pakietu O-Premium.');
        }
        
        // Dla inwestorów premium
        if ($planType === 'premium' && 
            (!$user->hasActiveSubscription() || $user->plan_type === 'free')) {
            return redirect()->route('subscription')
                ->with('warning', 'Ta funkcja wymaga pakietu Premium.');
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
