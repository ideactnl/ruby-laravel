<?php

namespace App\Http\Controllers\Api\MedicalSpecialist;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Services\PbacExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MedicalSpecialistController extends Controller
{
    private PbacExportService $pbacExportService;

    public function __construct(PbacExportService $pbacExportService)
    {
        $this->pbacExportService = $pbacExportService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return session('medical_specialist_id')
            ? redirect()->route('medical-specialist.dashboard')
            : view('medical-specialist.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        if (session('medical_specialist_id')) {
            return redirect()->route('medical-specialist.dashboard');
        }

        $credentials = $request->validate([
            'registration_number' => 'required|string',
            'pin' => 'required|string|min:4|max:6|regex:/^\d+$/',
        ]);

        $participant = Participant::where('registration_number', $credentials['registration_number'])
            ->where('allow_medical_specialist_login', true)
            ->first();

        $isValid = $participant &&
            $participant->medical_specialist_temporary_pin &&
            Hash::check($credentials['pin'], $participant->medical_specialist_temporary_pin) &&
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
     * Show dashboard with chart and filters
     */
    public function dashboard(Request $request, PbacExportService $pbacExportService)
    {
        $participant = $this->getAuthenticatedParticipant();
        if (! $participant) {
            return $this->redirectToLoginWithError();
        }

        $preset = $request->input('preset', 'month');
        $today = now();

        switch ($preset) {
            case 'week':
                $startDate = $today->startOfWeek()->toDateString();
                $endDate = $today->endOfWeek()->toDateString();
                break;
            case 'year':
                $startDate = $today->startOfYear()->toDateString();
                $endDate = $today->endOfYear()->toDateString();
                break;
            case 'custom':
                $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ]);
                $startDate = $request->start_date;
                $endDate = $request->end_date;
                break;
            case 'month':
            default:
                $startDate = $today->startOfMonth()->toDateString();
                $endDate = $today->endOfMonth()->toDateString();
                break;
        }

        $pbacRecords = \App\Models\Pbac::where('participant_id', $participant->id)
            ->whereBetween('reported_date', [$startDate, $endDate])
            ->orderBy('reported_date')
            ->get();

        $chartData = $pbacExportService->getChartDataFormatted($pbacRecords);

        return view('medical-specialist.dashboard', [
            'participant' => $participant,
            'expiryDate' => optional($participant->medical_specialist_temporary_pin_expires_at)?->format('M j, Y H:i'),
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'preset' => $preset,
        ]);
    }

    /**
     * Export PBAC data to Excel, CSV, or JSON
     */
    public function exportPbacData(Request $request)
    {
        $participant = $this->getAuthenticatedParticipant();
        if (! $participant) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        return $this->pbacExportService->exportForParticipant($request, $participant->id);
    }

    /**
     * Export PBAC chart as PDF
     */
    public function exportPbacPdf(Request $request)
    {
        $participant = $this->getAuthenticatedParticipant();
        if (! $participant) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        return $this->pbacExportService->exportPdfChartForParticipant($request, $participant->id);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        session()->forget('medical_specialist_id');

        return redirect()->route('medical-specialist.login');
    }

    /**
     * Get authenticated participant (helper)
     */
    protected function getAuthenticatedParticipant(): ?Participant
    {
        $participantId = session('medical_specialist_id');

        return $participantId ? Participant::find($participantId) : null;
    }

    /**
     * Redirect to login with error (helper)
     */
    protected function redirectToLoginWithError()
    {
        session()->forget('medical_specialist_id');

        return redirect()->route('medical-specialist.login')
            ->with('error', 'Please log in as a medical specialist.');
    }
}
