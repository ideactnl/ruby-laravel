<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfParticipantUnauthenticated
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Auth::guard('participant-web')->check()) {
            return redirect()->route('participant.web.login');
        }

        return $next($request);
    }
}
