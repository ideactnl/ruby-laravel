<?php

namespace App\Http\Controllers\Api\MedicalSpecialist;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Pbac;
use App\Services\ExportTrackingService;
use App\Services\PbacExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MedicalSpecialistController extends Controller
{
    private PbacExportService $pbacExportService;

    private ExportTrackingService $exportTrackingService;

    public function __construct(PbacExportService $pbacExportService, ExportTrackingService $exportTrackingService)
    {
        $this->pbacExportService = $pbacExportService;
        $this->exportTrackingService = $exportTrackingService;
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

        $request->session()->regenerate();
        session(['medical_specialist_id' => $participant->id]);

        return redirect()->route('medical-specialist.dashboard');
    }

    /**
     * Show export data page with chart and filters
     */
    public function dashboard(Request $request)
    {
        $participant = Participant::find(session('medical_specialist_id'));

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

        $pbacRecords = Pbac::where('participant_id', $participant->id)
            ->whereBetween('reported_date', [$startDate, $endDate])
            ->orderBy('reported_date')
            ->get();

        $chartData = $this->pbacExportService->getChartDataFormatted($pbacRecords);

        return view('medical-specialist.export-data', [
            'participant' => $participant,
            'expiryDate' => optional($participant->medical_specialist_temporary_pin_expires_at)?->format('M j, Y H:i'),
            'chartData' => $chartData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'preset' => $preset,
        ]);
    }

    /**
     * Get chart data as JSON for AJAX requests
     */
    public function getChartData(Request $request)
    {
        $participant = Participant::find(session('medical_specialist_id'));

        try {
            $validated = $request->validate([
                'preset' => ['nullable', 'in:month,quarter,year,custom'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $preset = $request->input('preset', 'month');
        $today = now();

        switch ($preset) {
            case 'quarter':
                $startDate = $today->startOfQuarter()->toDateString();
                $endDate = $today->endOfQuarter()->toDateString();
                break;
            case 'year':
                $startDate = $today->startOfYear()->toDateString();
                $endDate = $today->endOfYear()->toDateString();
                break;
            case 'custom':
                $startDate = $request->input('start_date', $today->startOfMonth()->toDateString());
                $endDate = $request->input('end_date', $today->endOfMonth()->toDateString());
                break;
            default:
                $startDate = $today->startOfMonth()->toDateString();
                $endDate = $today->endOfMonth()->toDateString();
                break;
        }

        $pbacRecords = Pbac::where('participant_id', $participant->id)
            ->whereBetween('reported_date', [$startDate, $endDate])
            ->orderBy('reported_date')
            ->get();

        $chartData = $this->pbacExportService->getChartDataFormatted($pbacRecords);

        return response()->json([
            'calendar' => $chartData,
        ]);
    }

    /**
     * Queue CSV export (PBAC calendar metrics)
     */
    public function exportPbacData(Request $request)
    {
        $participant = Participant::find(session('medical_specialist_id'));

        try {
            $validated = $request->validate([
                'preset' => ['nullable', 'in:month,quarter,year,custom'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        try {
            $job = $this->pbacExportService->queueParticipantCsv($request, $participant->id);

            return response()->json(['job' => $job], 202);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Queue chart export as PDF
     */
    public function exportPbacPdf(Request $request)
    {
        $participant = Participant::find(session('medical_specialist_id'));

        try {
            $validated = $request->validate([
                'chart_image' => 'required|string',
                'preset' => ['nullable', 'in:month,quarter,year,custom'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $imageData = $request->input('chart_image');
        if (! str_starts_with($imageData, 'data:image/png;base64,')) {
            return response()->json(['error' => 'Invalid image format'], 400);
        }

        try {
            $job = $this->pbacExportService->queueChartPdfFromImage($request, $participant->id);

            return response()->json(['job' => $job], 202);
        } catch (\Exception $e) {
            return response()->json(['error' => 'PDF export failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Get active export job
     */
    public function activeExport()
    {
        $participant = Participant::find(session('medical_specialist_id'));

        $active = $this->exportTrackingService->getActiveForParticipant($participant->id);

        return response()->json([
            'job' => $active ? $this->exportTrackingService->toPayload($active) : null,
        ]);
    }

    /**
     * Get export job by ID
     */
    public function exportStatus(string $jobId)
    {
        $participant = Participant::find(session('medical_specialist_id'));

        $job = $this->exportTrackingService->getById($jobId);
        if (! $job || $job->participant_id !== $participant->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'job' => $this->exportTrackingService->toPayload($job),
        ]);
    }

    /**
     * Download export by job ID
     */
    public function downloadExport(string $jobId)
    {
        $participant = Participant::find(session('medical_specialist_id'));

        $job = $this->exportTrackingService->getById($jobId);
        if (! $job || $job->participant_id !== $participant->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($job->status !== 'completed' || empty($job->file_path)) {
            return response()->json(['error' => 'File not ready'], 409);
        }

        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        if (! $disk->exists($job->file_path)) {
            return response()->json(['error' => 'File missing'], 410);
        }

        $absolutePath = $disk->path($job->file_path);
        $filename = basename($absolutePath);

        return response()->download($absolutePath, $filename);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        session()->forget('medical_specialist_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('medical-specialist.login');
    }

    /**
     * Get authenticated participant (helper)
     */
    protected function getAuthenticatedParticipant(): ?Participant
    {
        $participantId = session('medical_specialist_id');
        if (! $participantId) {
            return null;
        }

        $participant = Participant::find($participantId);
        if (! $participant) {
            return null;
        }

        if (! $participant->allow_medical_specialist_login ||
            ! $participant->medical_specialist_temporary_pin ||
            ($participant->medical_specialist_temporary_pin_expires_at &&
                now()->gt($participant->medical_specialist_temporary_pin_expires_at))) {
            session()->forget('medical_specialist_id');

            return null;
        }

        return $participant;
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
