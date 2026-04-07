<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminerSudoMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $verifiedUntil = session('adminer_verified_until');

        if (! $verifiedUntil || now()->greaterThan($verifiedUntil)) {
            session()->forget(['adminer_verified_until', 'adminer_challenge_passed']);

            return redirect()->route('admin.db-verify.show')
                ->with('warning', 'Please verify your identity to access the database.');
        }

        return $next($request);
    }
}
