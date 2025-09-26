<?php

namespace App\Http\Middleware;

use App\Models\Participant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicalSpecialistAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $participantId = session('medical_specialist_id');

        if (! $participantId) {
            return redirect('/medical-specialist/login')
                ->withErrors(['error' => 'Please log in to access this page.']);
        }

        $participant = Participant::find($participantId);

        if (! $participant) {
            session()->forget('medical_specialist_id');

            return redirect('/medical-specialist/login')
                ->withErrors(['error' => 'Invalid session. Please log in again.']);
        }

        if (! $participant->allow_medical_specialist_login ||
            ! $participant->medical_specialist_temporary_pin ||
            ($participant->medical_specialist_temporary_pin_expires_at &&
                now()->gt($participant->medical_specialist_temporary_pin_expires_at))) {

            session()->forget('medical_specialist_id');

            return redirect('/medical-specialist/login')
                ->withErrors(['error' => 'Your access has expired or been revoked. Please contact the participant to renew access.']);
        }

        return $next($request);
    }
}
