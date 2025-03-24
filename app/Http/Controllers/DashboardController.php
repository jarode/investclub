<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Wyświetla dashboard dla zalogowanego użytkownika.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Pobieranie dodatkowych informacji o subskrypcji ze Stripe
        $subscriptionDetails = null;
        if ($user->stripe_subscription_id && $user->hasActiveSubscription()) {
            try {
                $stripe = app(\Stripe\StripeClient::class);
                $subscriptionDetails = $stripe->subscriptions->retrieve(
                    $user->stripe_subscription_id,
                    ['expand' => ['customer', 'latest_invoice.payment_intent']]
                );
                
                // Logowanie informacji o subskrypcji do debugowania
                \Illuminate\Support\Facades\Log::info('Subscription details retrieved', [
                    'user_id' => $user->id,
                    'subscription_id' => $user->stripe_subscription_id,
                    'status' => $subscriptionDetails->status,
                    'current_period_end' => $subscriptionDetails->current_period_end,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to retrieve subscription details', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        if ($user->role === 'owner') {
            $recentInvestments = Investment::whereHas('project', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['project', 'user'])
            ->latest()
            ->get();
        } else {
            $recentInvestments = collect();
        }
        
        // Przekazujemy dane o statusie użytkownika do widoku
        return view('dashboard', [
            'user' => $user,
            'kycStatus' => $user->kyc_status ?? 'unverified',
            'subscriptionStatus' => $user->stripe_subscription_status,
            'subscriptionDetails' => $subscriptionDetails,
            'planName' => $this->getPlanName($user->plan_type),
            'nextPaymentDate' => $subscriptionDetails ? date('Y-m-d', $subscriptionDetails->current_period_end) : null,
            'recentInvestments' => $recentInvestments,
        ]);
    }
    
    /**
     * Pobiera przyjazną nazwę planu na podstawie typu.
     *
     * @param string|null $planType
     * @return string
     */
    private function getPlanName(?string $planType): string
    {
        return match($planType) {
            'free-investor' => 'I-Free (Darmowy plan dla inwestorów)',
            'premium-investor' => 'I-Premium (Premium dla inwestorów)',
            'premium-owner' => 'O-Premium (Premium dla właścicieli projektów)',
            default => $planType ?? 'Brak planu'
        };
    }
    
    /**
     * Wyświetla dashboard dla właścicieli projektów z planem O-Premium.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function projectOwner(Request $request)
    {
        $user = $request->user();
        
        // Pobierz projekty należące do użytkownika
        $projects = Project::where('owner_id', $user->id)
                           ->orderBy('created_at', 'desc')
                           ->take(5)
                           ->get();
        
        // Pobierz inwestycje w projekty użytkownika
        $investments = Investment::whereHas('project', function ($query) use ($user) {
                                    $query->where('owner_id', $user->id);
                                })
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();
        
        // Statystyki dla właściciela projektów
        $stats = [
            'projectsCount' => Project::where('owner_id', $user->id)->count(),
            'activeProjectsCount' => Project::where('owner_id', $user->id)
                                          ->where('status', 'active')
                                          ->count(),
            'totalInvestments' => Investment::whereHas('project', function ($query) use ($user) {
                                        $query->where('owner_id', $user->id);
                                    })->count(),
            'totalRaised' => Investment::whereHas('project', function ($query) use ($user) {
                                      $query->where('owner_id', $user->id);
                                  })
                                  ->where('status', 'completed')
                                  ->sum('amount'),
        ];
        
        return view('project-owner.dashboard', compact('user', 'projects', 'investments', 'stats'));
    }
} 