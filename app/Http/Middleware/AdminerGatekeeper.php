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

        if (! $this->verifyBasicAuth($request)) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Adminer Server Access"',
            ]);
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

        if (strpos($ip, ':') !== false) {
            return $ip === '::1' && ($range === '::1' || $range === '::1/128');
        }

        $ip_long = ip2long($ip);
        $net_long = ip2long($net);
        $mask_bin = ~((1 << (32 - (int) $mask)) - 1);

        if ($ip_long === false || $net_long === false) {
            return false;
        }

        return ($ip_long & $mask_bin) === ($net_long & $mask_bin);
    }

    /**
     * Verify Basic Auth against the server htpasswd file.
     */
    private function verifyBasicAuth(Request $request): bool
    {
        $user = $request->getUser();
        $pass = $request->getPassword();

        if (! $user || ! $pass) {
            return false;
        }

        $htpasswdFile = storage_path('app/adminer/.htpasswd-server');

        $lines = file($htpasswdFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }

            [$storedUser, $storedHash] = explode(':', $line, 2);

            if ($user === $storedUser && password_verify($pass, $storedHash)) {
                return true;
            }
        }

        return false;
    }
}
