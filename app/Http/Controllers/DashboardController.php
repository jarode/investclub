<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
} 