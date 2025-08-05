<?php

namespace App\Http\Controllers\Api\MedicalSpecialist;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MedicalSpecialistController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (session('medical_specialist_id')) {
            return redirect()->route('medical-specialist.dashboard');
        }

        return view('medical-specialist.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        if (session('medical_specialist_id')) {
            return redirect()->route('medical-specialist.dashboard');
        }

        $request->validate([
            'registration_number' => 'required|string',
            'pin' => 'required|string|min:4|max:6|regex:/^\\d+$/',
        ]);

        $participant = Participant::where('registration_number', $request->registration_number)
            ->where('allow_medical_specialist_login', true)
            ->first();

        $isValid = $participant &&
                $participant->medical_specialist_temporary_pin &&
                Hash::check($request->pin, $participant->medical_specialist_temporary_pin) &&
                (! $participant->medical_specialist_temporary_pin_expires_at ||
                now()->lte($participant->medical_specialist_temporary_pin_expires_at));

        if (! $isValid) {
            return back()->withErrors([
                'pin' => 'Invalid credentials or PIN expired.',
            ])->withInput();
        }

        session(['medical_specialist_id' => $participant->id]);

        return redirect()->route('medical-specialist.dashboard');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $participantId = session('medical_specialist_id');
        if (! $participantId) {
            return redirect()->route('medical-specialist.login')
                ->with('error', 'Please log in as a medical specialist.');
        }

        $participant = Participant::find($participantId);
        if (! $participant) {
            session()->forget('medical_specialist_id');

            return redirect()->route('medical-specialist.login')
                ->with('error', 'Participant not found.');
        }

        return view('medical-specialist.dashboard', [
            'participant' => $participant,
            'expiryDate' => $participant->medical_specialist_temporary_pin_expires_at
                ? $participant->medical_specialist_temporary_pin_expires_at->format('M j, Y H:i')
                : null,
        ]);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session()->forget('medical_specialist_id');

        return redirect()->route('medical-specialist.login');
    }
}
