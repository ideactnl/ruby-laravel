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

        return redirect()->route('admin.database.index');
    }
}
