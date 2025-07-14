<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WebLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('web_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $participant = Participant::where('registration_number', $request->registration_number)->first();
        if ($participant && Hash::check($request->password, $participant->password)) {
            Auth::login($participant, $request->boolean('remember'));

            return redirect()->intended('/dashboard');
        }

        return back()->withInput($request->only('registration_number'))
            ->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/web-login');
    }

    // Show dashboard (protected)
    public function dashboard()
    {
        return view('dashboard', ['participant' => Auth::user()]);
    }
}
