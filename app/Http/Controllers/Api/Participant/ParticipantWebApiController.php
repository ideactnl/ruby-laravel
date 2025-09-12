<?php

namespace App\Http\Controllers\Api\Participant;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Pbac;
use App\Services\PbacExportService;
use Barryvdh\DomPDF\Facade\Pdf;
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
     * Export CSV (PBAC calendar metrics)
     *
     * @authenticated
     *
     * @queryParam preset string optional One of: month, quarter, year, custom. Default: month.
     * @queryParam start_date date optional Y-m-d; required when preset=custom.
     * @queryParam end_date date optional Y-m-d; required when preset=custom.
     *
     * @response 200 text/csv CSV file streamed to the client
     */
    public function exportPbacData(Request $request, PbacExportService $exportService)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'preset' => ['nullable', 'in:month,quarter,year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $q = Pbac::query()->forParticipant($participant->id);
        $q = CommonHelper::applyDateFilters($q, $request)->orderBy('reported_date');

        $rows = $q->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pbac_export.csv"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'reported_date', 'pbac_score_per_day', 'spotting_yes_no', 'pain_score_per_day', 'influence_factor', 'pain_medication', 'quality_of_life', 'energy_level', 'complaints_with_defecation', 'complaints_with_urinating', 'quality_of_sleep', 'exercise',
            ]);
            $i = 0;
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->reported_date,
                    (int) $r->pbac_score_per_day,
                    (int) $r->spotting_yes_no,
                    (int) $r->pain_score_per_day,
                    (int) $r->influence_factor,
                    (int) $r->pain_medication,
                    (int) $r->quality_of_life,
                    (int) $r->energy_level,
                    (int) $r->complaints_with_defecation,
                    (int) $r->complaints_with_urinating,
                    (int) $r->quality_of_sleep,
                    (int) $r->exercise,
                ]);
                if ((++$i % 200) === 0) {
                    fflush($out);
                    if (function_exists('ob_flush')) {
                        @ob_flush();
                    }
                    flush();
                }
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export chart as PDF
     *
     * @authenticated
     *
     * @bodyParam chart_image string required Base64 image data URL (data:image/png;base64,...)
     * @bodyParam preset string optional One of: month, quarter, year, custom
     * @bodyParam start_date date optional Y-m-d; used when preset=custom
     * @bodyParam end_date date optional Y-m-d; used when preset=custom
     *
     * @response 200 application/pdf Binary PDF streamed to the client
     */
    public function exportChartPdf(Request $request)
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

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $preset = $request->input('preset');

        try {
            $pdf = Pdf::loadView('pdf.participant-pbac-chart', [
                'imageBase64' => $imageData,
                'preset' => $preset,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="pbac_chart.pdf"',
            ]);
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: '.$e->getMessage());

            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
}
