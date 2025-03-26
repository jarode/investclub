<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycIsVerified
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
        if ($request->user() && $request->user()->kyc_status !== 'verified') {
            return redirect()->route('kyc.verify')
                ->with('warning', __('KYC verification is required before accessing this feature'));
        }

        return $next($request);
    }
} 