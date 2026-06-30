<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->current_business_id) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
