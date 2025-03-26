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
    public function handle(Request $request, Closure $next, $planType): Response
    {
        $user = $request->user();
        
        // Logowanie pełnych danych użytkownika
        \Illuminate\Support\Facades\Log::info('Middleware CheckSubscriptionPlan - dane użytkownika', [
            'user_id' => $user->id,
            'name' => $user->name,
            'plan_type' => $user->plan_type ?? 'brak',
            'subscription_type' => $user->subscription_type ?? 'brak',
            'stripe_subscription_status' => $user->stripe_subscription_status ?? 'brak',
            'wymagany_plan' => $planType,
            'aktywna_subskrypcja' => $user->hasActiveSubscription() ? 'TAK' : 'NIE',
            'może_zarządzać_projektami' => $user->canManageProjects() ? 'TAK' : 'NIE',
            'uri' => $request->getRequestUri()
        ]);
        
        // Jeśli użytkownik nie ma aktywnej subskrypcji, przekieruj do strony subskrypcji
        if (!$user->hasActiveSubscription()) {
            \Illuminate\Support\Facades\Log::info('Brak aktywnej subskrypcji - przekierowanie', ['user_id' => $user->id]);
            return redirect()->route('subscription')
                ->with('warning', __('Active subscription is required before accessing this feature'));
        }
        
        // Administratorzy zawsze mają dostęp
        if ($user->isAdmin()) {
            \Illuminate\Support\Facades\Log::info('Dostęp przyznany dla administratora', ['user_id' => $user->id]);
            return $next($request);
        }
        
        // Sprawdź typ planu
        switch ($planType) {
            case 'owner':
                if ($user->hasPlanType('premium-owner') || $user->hasPlanType('owner')) {
                    \Illuminate\Support\Facades\Log::info('Dostęp do funkcji właściciela przyznany', ['user_id' => $user->id]);
                    return $next($request);
                }
                break;
                
            case 'premium':
                if ($user->hasPlanType('premium-investor') || $user->hasPlanType('premium-owner')) {
                    \Illuminate\Support\Facades\Log::info('Dostęp do funkcji premium przyznany', ['user_id' => $user->id]);
                    return $next($request);
                }
                break;
                
            case 'any':
                \Illuminate\Support\Facades\Log::info('Dostęp do podstawowych funkcji przyznany', ['user_id' => $user->id]);
                return $next($request);
                
            default:
                \Illuminate\Support\Facades\Log::warning('Nieznany typ planu wymagany', ['plan' => $planType, 'user_id' => $user->id]);
                break;
        }
        
        \Illuminate\Support\Facades\Log::info('Odmowa dostępu - nieprawidłowy plan', ['user_id' => $user->id, 'wymagany_plan' => $planType]);
        
        // Jeśli użytkownik nie ma wymaganego planu, przekieruj do strony subskrypcji
        return redirect()->route('subscription')
            ->with('warning', __('You need a :plan subscription to access this feature', ['plan' => $this->getPlanName($planType)]));
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

    /**
     * Zwraca czytelną nazwę planu na podstawie kodu
     */
    private function getPlanName(string $planType): string
    {
        return match($planType) {
            'owner' => __('Premium for project owners (O-Premium)'),
            'premium' => __('Premium for investors (I-Premium)'),
            'any' => __('Any'),
            default => $planType
        };
    }
}
