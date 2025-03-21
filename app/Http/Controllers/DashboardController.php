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
        
        // Przekazujemy dane o statusie użytkownika do widoku
        return view('dashboard', [
            'user' => $user,
            'kycStatus' => $user->kyc_status,
            'subscriptionStatus' => $user->stripe_subscription_status
        ]);
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