<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminerVerificationController extends Controller
{
    /**
     * Show the Identity Challenge (Password re-verification).
     */
    public function show(Request $request)
    {
        return view('admin.db-challenge');
    }

    /**
     * Process the Challenge (Verify Password) and grant access.
     */
    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The password provided is incorrect.']);
        }

        session(['adminer_verified_until' => now()->addMinutes(10)]);

        return redirect()->route('admin.server-auth.show');
    }

    /**
     * Show the Server-Level Authentication Form.
     */
    public function showServerAuth(Request $request)
    {
        if (! session('adminer_verified_until') || now()->greaterThan(session('adminer_verified_until'))) {
            return redirect()->route('admin.db-verify.show');
        }

        return view('admin.server-auth');
    }

    /**
     * Process the Server-Level Authentication.
     */
    public function verifyServerAuth(Request $request)
    {
        if (! session('adminer_verified_until') || now()->greaterThan(session('adminer_verified_until'))) {
            return redirect()->route('admin.db-verify.show');
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $htpasswdFile = storage_path('app/adminer/.htpasswd-server');
        if (! file_exists($htpasswdFile)) {
            abort(403, 'Adminer server-level password not configured. Please run: php artisan adminer:password {username} {password}');
        }

        $lines = file($htpasswdFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $isAuthenticated = false;

        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }

            [$storedUser, $storedHash] = explode(':', $line, 2);

            if ($request->username === $storedUser && password_verify($request->password, $storedHash)) {
                $isAuthenticated = true;
                break;
            }
        }

        if (! $isAuthenticated) {
            return back()->withErrors(['password' => 'The server credentials provided are incorrect.']);
        }

        session(['adminer_server_auth_passed' => now()->addMinutes(10)]);

        return redirect()->route('admin.database.index');
    }
}
