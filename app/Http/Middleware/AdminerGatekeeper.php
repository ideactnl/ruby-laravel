<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminerGatekeeper
{
    /**
     * Security sequence Gate 2 & 3: IP Whitelist and Basic Auth.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isIpAllowed($request->ip())) {
            Log::warning('Adminer Access Denied for IP: '.$request->ip());
            abort(403, 'Unauthorized IP Address.');
        }

        $htpasswdFile = storage_path('app/adminer/.htpasswd-server');
        if (! file_exists($htpasswdFile)) {
            abort(403, 'Adminer server-level password not configured. Please run: php artisan adminer:password {username} {password}');
        }

        if (! session('adminer_server_auth_passed') || now()->greaterThan(session('adminer_server_auth_passed'))) {
            Log::warning('Adminer Server-Level Auth Missing for IP: '.$request->ip().'. Redirecting to form.');

            return redirect()->route('admin.server-auth.show');
        }

        return $next($request);
    }

    /**
     * Verify if the IP is allowed (CIDR matching).
     */
    private function isIpAllowed(string $ip): bool
    {
        $defaultAllowed = [
            '145.117.0.0/16',
            '127.0.0.1',
            '::1',
        ];

        $envIps = explode(',', (string) config('database.adminer_allowed_ips', ''));
        $allowed = array_filter(array_merge($defaultAllowed, array_map('trim', $envIps)));

        foreach ($allowed as $range) {
            if ($this->ipInNetwork($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in a CIDR network range.
     */
    private function ipInNetwork(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$net, $mask] = explode('/', $range, 2);
        $mask = (int) $mask;

        $ip_packed = @inet_pton($ip);
        $net_packed = @inet_pton($net);

        if ($ip_packed === false || $net_packed === false || strlen($ip_packed) !== strlen($net_packed)) {
            return false;
        }

        $bits = strlen($ip_packed) * 8;
        if ($mask < 0 || $mask > $bits) {
            return false;
        }

        for ($i = 0; $i < strlen($ip_packed); $i++) {
            $mask_byte = 0;
            if ($mask > 0) {
                $m = min($mask, 8);
                $mask_byte = (0xFF << (8 - $m)) & 0xFF;
                $mask -= $m;
            }

            if ((ord($ip_packed[$i]) & $mask_byte) !== (ord($net_packed[$i]) & $mask_byte)) {
                return false;
            }
            if ($mask === 0) {
                break;
            }
        }

        return true;
    }
}
