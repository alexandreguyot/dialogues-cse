<?php

namespace App\Http\Middleware;

use App\Support\TwoFactor;
use Closure;

class EnsureTwoFactor
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user->hasTwoFactor() && ! TwoFactor::passed($user)) {
            return redirect()->route('two-factor.challenge');
        }

        if (! TwoFactor::satisfied($user)) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
