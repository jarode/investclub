<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionIsActive
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
        if ($request->user() && $request->user()->stripe_subscription_status !== 'active') {
            return redirect()->route('subscription')
                ->with('warning', 'Wymagana aktywna subskrypcja przed dostępem do tej funkcji.');
        }

        return $next($request);
    }
} 