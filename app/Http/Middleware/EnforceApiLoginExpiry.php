<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceApiLoginExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

         if (Auth::guard('participant-web')->check() && session('api_login') === true) {
            $expiresAt = session('api_login_expires_at');

            if ($expiresAt) {
                $now = Carbon::now();

                // Hard expiry
                if ($now->greaterThanOrEqualTo($expiresAt)) {
                    Auth::guard('participant-web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()
                        ->route('participant.web.login')
                        ->withErrors(['error' => 'Session expired !']);
                }

                // Warning window (last 60 seconds)
                if ($now->diffInSeconds($expiresAt, false) <= 60) {
                    session()->put('show_expiry_warning', true);
                }
            }
        }
        
        return $next($request);
    }
}
