<?php

namespace App\Http\Controllers\Api\Participant;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Pbac;
use App\Services\ExportTrackingService;
use App\Services\PbacExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Participant Dashboard
 *
 * Endpoints for participant web dashboard authentication and data access using SPA-style (cookie-based) authentication.
 *
 * Flow overview:
 * - Initialize CSRF with `GET /sanctum/csrf-cookie`.
 * - Authenticate with `POST /api/v1/participant/login` using registration number + password.
 * - Subsequent requests send the session cookie automatically.
 * - Use `POST /api/v1/participant/logout` to end the session.
 */
class ParticipantWebApiController extends Controller
{
    /**
     * Login (session cookie)
     *
     * Authenticates a participant and starts a session for the web dashboard.
     *
     * @bodyParam registration_number string required Participant registration number. Example: participant123
     * @bodyParam password string required Password. Example: secret123
     *
     * @response 200 {"success": true, "participant": {"id": 1, "registration_number": "participant123"}}
     * @response 422 {"message":"The given data was invalid.","errors":{"registration_number":["The provided credentials are incorrect."]}}
     */
    public function login(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $participant = Participant::where('registration_number', $request->registration_number)->first();

        if (! $participant || ! Hash::check($request->password, $participant->password)) {
            throw ValidationException::withMessages([
                'registration_number' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::guard('participant-web')->login($participant);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'participant' => [
                'id' => $participant->id,
                'registration_number' => $participant->registration_number,
            ],
        ]);
    }

    /**
     * Logout
     *
     * Ends the current participant session.
     *
     * @response 200 {"success": true, "message": "Logged out successfully"}
     * @response 401 {"error": "Unauthenticated"}
     */
    public function logout(Request $request)
    {
        Auth::guard('participant-web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    /**
     * Dashboard data
     *
     * Returns participant flags and a date-filtered calendar collection with computed per-day metrics.
     * Used by the calendar, daily view, and export chart.
     *
     * @authenticated
     *
     * @queryParam preset string optional One of: month, quarter, year, custom. Default: month. Example: month
     * @queryParam start_date date optional Y-m-d; required when preset=custom. Example: 2025-09-01
     * @queryParam end_date date optional Y-m-d; required when preset=custom. Example: 2025-09-30
     *
     * @response 200 {"participant":{"id":1,"registration_number":"participant123"},"calendar":[{"reported_date":"2025-09-10","pbac_score_per_day":12,"spotting_yes_no":0,"pain_score_per_day":3,"influence_factor":2,"pain_medication":1,"quality_of_life":2,"energy_level":3,"complaints_with_defecation":0,"complaints_with_urinating":0,"quality_of_sleep":4,"exercise":1,"sleep_hours":6.5}]}
     * @response 401 {"error":"Unauthenticated"}
     */
    public function dashboard(Request $request)
    {
        $participant = Auth::guard('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $calendarBase = Pbac::query()
            ->forParticipant($participant->id);
        $calendarBase = CommonHelper::applyDateFilters($calendarBase, $request)
            ->orderBy('reported_date');

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pbac> $records */
        $records = $calendarBase->get();
        $calendar = $records->map(function (Pbac $r): array {
            $sleepHours = null;
            if (! empty($r->q17b) && ! empty($r->q17c)) {
                try {
                    $start = Carbon::createFromFormat('H:i', (string) $r->q17b);
                    $end = Carbon::createFromFormat('H:i', (string) $r->q17c);
                    if ($end->lessThanOrEqualTo($start)) {
                        $end->addDay();
                    }
                    $sleepHours = round($start->diffInMinutes($end) / 60, 1);
                } catch (\Exception $e) {
                    $sleepHours = null;
                }
            }

            return [
                'reported_date' => $r->reported_date,
                'pbac_score_per_day' => $r->pbac_score_per_day,
                'spotting_yes_no' => $r->spotting_yes_no,
                'pain_score_per_day' => $r->pain_score_per_day,
                'influence_factor' => $r->influence_factor,
                'pain_medication' => $r->pain_medication,
                'quality_of_life' => $r->quality_of_life,
                'energy_level' => $r->energy_level,
                'complaints_with_defecation' => $r->complaints_with_defecation,
                'complaints_with_urinating' => $r->complaints_with_urinating,
                'quality_of_sleep' => $r->quality_of_sleep,
                'exercise' => $r->exercise,
                'sleep_hours' => $sleepHours,
            ];
        });

        return response()->json([
            'participant' => [
                'id' => $participant->id,
                'registration_number' => $participant->registration_number,
                'enable_data_sharing' => $participant->enable_data_sharing,
                'opt_in_for_research' => $participant->opt_in_for_research,
            ],
            'calendar' => $calendar,
        ]);
    }

    /**
     * Daily data (single date)
     *
     * Returns the computed metrics for the specified date.
     *
     * @authenticated
     *
     * @queryParam date date required Target date (Y-m-d). Example: 2025-09-10
     *
     * @response 200 {"date":"2025-09-10","data":{"reported_date":"2025-09-10","pbac_score_per_day":7,"pain_score_per_day":3,"sleep_hours":6.5,"quality_of_life":2,"influence_factor":0,"pain_medication":1,"spotting_yes_no":0,"energy_level":3,"complaints_with_defecation":0,"complaints_with_urinating":0,"quality_of_sleep":4,"exercise":1}}
     */
    public function dailyData(Request $request)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $validated = $request->validate([
            'date' => ['required', 'date'],
        ]);
        $date = $validated['date'];

        $record = Pbac::query()
            ->forParticipant($participant->id)
            ->whereDate('reported_date', $date)
            ->orderBy('reported_date')
            ->first();

        if (! $record) {
            return response()->json(['date' => $date, 'data' => null]);
        }

        $sleepHours = null;
        if (! empty($record->q17b) && ! empty($record->q17c)) {
            try {
                $start = Carbon::createFromFormat('H:i', (string) $record->q17b);
                $end = Carbon::createFromFormat('H:i', (string) $record->q17c);
                if ($end->lessThanOrEqualTo($start)) {
                    $end->addDay();
                }
                $sleepHours = round($start->diffInMinutes($end) / 60, 1);
            } catch (\Exception $e) {
                $sleepHours = null;
            }
        }

        $data = [
            'reported_date' => $record->reported_date,
            'pbac_score_per_day' => $record->pbac_score_per_day,
            'spotting_yes_no' => $record->spotting_yes_no,
            'pain_score_per_day' => $record->pain_score_per_day,
            'influence_factor' => $record->influence_factor,
            'pain_medication' => $record->pain_medication,
            'quality_of_life' => $record->quality_of_life,
            'energy_level' => $record->energy_level,
            'complaints_with_defecation' => $record->complaints_with_defecation,
            'complaints_with_urinating' => $record->complaints_with_urinating,
            'quality_of_sleep' => $record->quality_of_sleep,
            'exercise' => $record->exercise,
            'sleep_hours' => $sleepHours,
        ];

        return response()->json([
            'date' => $date,
            'data' => $data,
        ]);
    }

    /**
     * Queue CSV export (PBAC calendar metrics)
     *
     * Queues a CSV export for the authenticated participant and returns a tracking job payload.
     *
     * @authenticated
     *
     * @queryParam preset string optional One of: month, quarter, year, custom. Default: month.
     * @queryParam start_date date optional Y-m-d; required when preset=custom.
     * @queryParam end_date date optional Y-m-d; required when preset=custom.
     *
     * @response 202 {"job":{"id":"uuid","type":"csv","status":"queued","progress":0,"file_path":null,"download_url":null}}
     * @response 401 {"error":"Unauthenticated"}
     */
    public function exportPbacData(Request $request, PbacExportService $exportService)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'preset' => ['nullable', 'in:month,quarter,year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $job = $exportService->queueParticipantCsv($request, $participant->id);

        return response()->json(['job' => $job], 202);
    }

    /**
     * Queue chart export as PDF
     *
     * Queues a PDF export (chart image provided by the client) for the authenticated participant
     * and returns a tracking job payload.
     *
     * @authenticated
     *
     * @bodyParam chart_image string required Base64 image data URL (data:image/png;base64,...) Example: data:image/png;base64,iVBORw...
     * @bodyParam preset string optional One of: month, quarter, year, custom
     * @bodyParam start_date date optional Y-m-d; used when preset=custom
     * @bodyParam end_date date optional Y-m-d; used when preset=custom
     *
     * @response 202 {"job":{"id":"uuid","type":"pdf","status":"queued","progress":0,"file_path":null,"download_url":null}}
     * @response 400 {"error":"Invalid image format"}
     * @response 401 {"error":"Unauthenticated"}
     */
    public function exportChartPdf(Request $request, PbacExportService $exportService)
    {
        $request->validate([
            'chart_image' => 'required|string',
            'preset' => ['nullable', 'in:month,quarter,year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        $imageData = $request->input('chart_image');
        if (! str_starts_with($imageData, 'data:image/png;base64,')) {
            return response()->json(['error' => 'Invalid image format'], 400);
        }
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $job = $exportService->queueChartPdfFromImage($request, $participant->id);

        return response()->json(['job' => $job], 202);
    }

    /**
     * Get active export job
     *
     * Returns the most recent queued/processing job for the authenticated participant.
     *
     * @authenticated
     *
     * @response 200 {"job": {"id":"uuid","type":"csv","status":"processing","progress":25}}
     * @response 200 {"job": null}
     * @response 401 {"error":"Unauthenticated"}
     */
    public function activeExport(Request $request, ExportTrackingService $tracker)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $active = $tracker->getActiveForParticipant($participant->id);

        return response()->json([
            'job' => $active ? $tracker->toPayload($active) : null,
        ]);
    }

    /**
     * Get export job by ID
     *
     * Returns the export job payload by its ID. The job must belong to the authenticated participant.
     *
     * @authenticated
     *
     * @urlParam jobId string required The export job UUID. Example: 3b0a3a80-5f8a-4a28-bb79-fd2c4b15c9ef
     *
     * @response 200 {"job": {"id":"uuid","type":"csv","status":"completed","progress":100,"file_path":"exports/participant/1/pbac_export_....csv"}}
     * @response 401 {"error":"Unauthenticated"}
     * @response 404 {"error":"Not found"}
     */
    public function exportStatus(string $jobId, ExportTrackingService $tracker)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $job = $tracker->getById($jobId);
        if (! $job || $job->participant_id !== $participant->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'job' => $tracker->toPayload($job),
        ]);
    }

    /**
     * Download export by job ID
     *
     * Streams a completed export file for the authenticated participant (signed URL).
     *
     * @authenticated
     *
     * @urlParam jobId string required The export job UUID. Example: 3b0a3a80-5f8a-4a28-bb79-fd2c4b15c9ef
     *
     * @response 200 application/octet-stream File content
     * @response 401 {"error":"Unauthenticated"}
     * @response 404 {"error":"Not found"}
     * @response 409 {"error":"File not ready"}
     */
    public function downloadExport(string $jobId, ExportTrackingService $tracker)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $job = $tracker->getById($jobId);
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
}
