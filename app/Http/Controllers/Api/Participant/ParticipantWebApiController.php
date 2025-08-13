<?php

namespace App\Http\Controllers\Api\Participant;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Pbac;
use App\Services\PbacExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Participant Dashboard
 *
 * Endpoints for participant web dashboard authentication and data access using SPA-style (cookie-based) authentication.
 *
 * These endpoints are intended for use by the participant dashboard (Blade + Alpine.js) and rely on Laravel Sanctum's cookie-based session authentication. Participants log in with their registration number and password, and all subsequent requests are authenticated via session cookie. Admin and participant sessions are kept separate.
 *
 * **How SPA Auth Works:**
 * - The frontend first calls `/sanctum/csrf-cookie` to initialize CSRF protection.
 * - Login is performed via POST `/api/v1/participant/login`.
 * - On success, a session cookie is issued. All further requests (e.g., dashboard data, logout) use this cookie for authentication.
 * - The dashboard and logout endpoints require the session cookie and CSRF token.
 */
class ParticipantWebApiController extends Controller
{
    /**
     * Login (SPA session/cookie)
     *
     * Authenticates a participant using registration number and password. Issues a session cookie for subsequent dashboard requests.
     *
     * @bodyParam registration_number string required The participant's registration number. Example: participant123
     * @bodyParam password string required The participant's password. Example: mypassword
     *
     * @response 200 {
     *   "success": true,
     *   "participant": { "id": 1, "registration_number": "participant123" }
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": { "registration_number": ["The provided credentials are incorrect."] }
     * }
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
     * Logout (SPA session/cookie)
     *
     * Logs out the authenticated participant by invalidating the session cookie.
     *
     * **Requires authentication via session cookie.**
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logged out successfully"
     * }
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     */
    public function logout(Request $request)
    {
        Auth::guard('participant-web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }

    /**
     * Get dashboard data (SPA session/cookie)
     *
     * Returns the authenticated participant's dashboard data. Requires a valid session cookie.
     *
     * **Requires authentication via session cookie.**
     *
     * @authenticated
     *
     * @response 200 {
     *   "participant": {
     *     "id": 1,
     *     "registration_number": "participant123",
     *     "enable_data_sharing": true,
     *     "opt_in_for_research": false
     *   }
     * }
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     */
    public function dashboard(Request $request)
    {
        $participant = Auth::guard('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'participant' => [
                'id' => $participant->id,
                'registration_number' => $participant->registration_number,
                'enable_data_sharing' => $participant->enable_data_sharing,
                'opt_in_for_research' => $participant->opt_in_for_research,
            ],
        ]);
    }

    /**
     * Get PBAC chart data (date-filtered)
     *
     * Returns PBAC data for use in charts for the authenticated participant, with optional date filtering.
     *
     * **Requires authentication via session cookie.**
     *
     * @authenticated
     *
     * @queryParam from_date date optional Filter records from this date (format: Y-m-d). Example: 2025-07-01
     * @queryParam to_date date optional Filter records up to this date (format: Y-m-d). Example: 2025-07-31
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "reported_date": "2025-07-01",
     *       "pbac_score_per_day": 14,
     *       "pain_score_per_day": 7,
     *       "quality_of_life": 1,
     *       "energy_level": 3,
     *       "spotting_yes_no": yes,
     *       "influence_factor": 5,
     *       "pain_medication": 0,
     *       "complaints_with_defecation": 1 ,
     *       "complaints_with_urinating": 1,
     *       "quality_of_sleep": 4,
     *       "exercise": 0
     *     }
     *   ]
     * }
     */
    public function showPbacChartData(Request $request)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $query = Pbac::where('participant_id', $participant->id)
            ->orderBy('reported_date');

        $query = CommonHelper::applyDateFilters($query, $request);

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    /**
     * Export PBAC data to Excel.
     *
     * @authenticated
     *
     * @queryParam filter string required The date range filter. Options: 'weekly', 'monthly', 'yearly', 'custom'. Example: 'monthly'
     * @queryParam from date optional The start date for the custom range (Y-m-d). Required if filter is 'custom'. Example: 2025-07-01
     * @queryParam to date optional The end date for the custom range (Y-m-d). Required if filter is 'custom'. Example: 2025-07-31
     */
    public function exportPbacData(Request $request, PbacExportService $exportService)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return $exportService->exportForParticipant($request, $participant->id);
    }

    /**
     * Export the PBAC chart as a PDF.
     *
     * @authenticated
     *
     * @bodyParam chart_data_url string required The base64 chart image data (data:image/png;base64,...)
     * @bodyParam preset string optional Filter preset used for labeling. Example: month
     * @bodyParam start_date date optional Custom start date if preset is 'custom'.
     * @bodyParam end_date date optional Custom end date if preset is 'custom'.
     */
    public function exportChartPdf(Request $request)
    {
        $request->validate([
            'chart_image' => 'required|string',
        ]);

        $imageData = $request->input('chart_image');
        if (! str_starts_with($imageData, 'data:image/png;base64,')) {
            return response()->json(['error' => 'Invalid image format'], 400);
        }

        $preset = $request->input('preset', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

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
            // log actual error
            \Log::error('PDF generation failed: '.$e->getMessage());

            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
}
