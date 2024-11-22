<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureTwoFactorIsVerified
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user has a two_factor_secret and is not on the verification page
        if ($user && !$user->two_factor_secret== null && !$request->is('two-factor', 'verify-2fa')) {
            return redirect()->route('two.factor.form');
        }

        return $next($request);
    }
}

