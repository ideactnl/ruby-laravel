<?php

namespace App\Http\Controllers\Api\Participant;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Pbac;
use App\Services\CmsApiCallService;
use App\Services\ExportTrackingService;
use App\Services\ParticipantActivityLogService;
use App\Services\ParticipantSessionService;
use App\Services\PbacExportService;
use App\Services\PbacService;
use App\Services\VideoService;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @group Participant Dashboard
 *
 * Endpoints for participant web dashboard authentication and data access using session & cookie based authentication.
 */
class ParticipantWebApiController extends Controller
{
    /**
     * Login (session cookie)
     *
     * Authenticates a participant and starts a session for the web dashboard.
     *
     * @unauthenticated
     *
     * @bodyParam registration_number string required Participant registration number. Example: participant123
     * @bodyParam password string required Password. Example: secret123
     *
     * @response 200 {"success": true, "participant": {"id": 1, "registration_number": "participant123"}}
     * @response 422 {"message":"The given data was invalid.","errors":{"registration_number":["The provided credentials are incorrect."]}}
     *
     * @responseField success boolean Whether the login was successful
     * @responseField participant object The authenticated participant data
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

        // Start session tracking
        app(ParticipantSessionService::class)->startSession($participant);

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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session to logout</b>
     *
     * @unauthenticated
     *
     * @response 200 {"success": true, "message": "Logged out successfully"}
     * @response 401 {"error": "Unauthenticated"}
     *
     * @responseField success boolean Whether the logout was successful
     * @responseField message string Confirmation message
     */
    public function logout(Request $request)
    {

        if ($request->session()->has('api_auth_token')) {
            Cache::forget('dashboard_login_token:'.hash('sha256', $request->session()->get('api_auth_token')));
        }

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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
     *
     * @queryParam preset string optional One of: month, quarter, year, custom. Default: month. Example: month
     * @queryParam start_date date optional Y-m-d; required when preset=custom. Example: 2025-09-01
     * @queryParam end_date date optional Y-m-d; required when preset=custom. Example: 2025-09-30
     *
     * @response 200 {"participant":{"id":1,"registration_number":"participant123"},"calendar":[{"reported_date":"2025-09-10","pillars":{"blood_loss":{"answered":true,"amount":12,"severity":"moderate"},"pain":{"answered":true,"value":3},"impact":{"answered":true,"gradeYourDay":2},"general_health":{"answered":true,"energyLevel":3},"sleep":{"answered":true,"calculatedHours":6.5},"exercise":{"answered":true,"any":true}},"sleep_hours":6.5}]}
     * @response 401 {"error":"Unauthenticated"}
     *
     * @responseField participant object The authenticated participant information
     * @responseField calendar array Array of PBAC records with computed pillar data
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
        $calendar = [];
        foreach ($records as $r) {
            $calendar[] = [
                'reported_date' => $r->reported_date->format('Y-m-d'),
                'pillars' => [
                    'blood_loss' => $r->blood_loss,
                    'pain' => $r->pain,
                    'impact' => $r->impact,
                    'general_health' => $r->general_health,
                    'mood' => $r->mood,
                    'stool_urine' => $r->stool_urine,
                    'sleep' => $r->sleep,
                    'diet' => $r->diet,
                    'exercise' => $r->exercise,
                    'sex' => $r->sex,
                    'notes' => $r->notes,
                ],
                'sleep_hours' => $r->sleep['calculatedHours'] ?? null,
            ];
        }

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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
     *
     * @queryParam date date required Target date (Y-m-d). Example: 2025-09-10
     *
     * @response 200 {"date":"2025-09-10","data":{"reported_date":"2025-09-10","pillars":{"blood_loss":{"answered":true,"amount":7},"pain":{"answered":true,"value":3},"impact":{"answered":true,"gradeYourDay":2},"general_health":{"answered":true,"energyLevel":3},"sleep":{"answered":true,"calculatedHours":6.5},"exercise":{"answered":true,"any":true}},"sleep_hours":6.5}}
     * @response 401 {"error":"Unauthenticated"}
     * @response 422 {"error":"Invalid query parameters.","errors":{"date":["The date field is required."]}}
     */
    public function dailyData(Request $request)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $validated = $request->query();

        // Validate query parameters
        $validator = validator($validated, [
            'date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid query parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $date = $validated['date'];

        $record = Pbac::query()
            ->forParticipant($participant->id)
            ->whereDate('reported_date', $date)
            ->orderBy('reported_date')
            ->first();

        if (! $record) {
            return response()->json(['date' => $date, 'data' => null]);
        }

        $data = [
            'reported_date' => $record->reported_date,
            'pillars' => [
                'blood_loss' => $record->blood_loss,
                'pain' => $record->pain,
                'impact' => $record->impact,
                'general_health' => $record->general_health,
                'mood' => $record->mood,
                'stool_urine' => $record->stool_urine,
                'sleep' => $record->sleep,
                'diet' => $record->diet,
                'exercise' => $record->exercise,
                'sex' => $record->sex,
                'notes' => $record->notes,
            ],
            'sleep_hours' => $record->sleep['calculatedHours'] ?? null,
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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
     *
     * @queryParam preset string optional One of: month, quarter, year, custom. Default: month. Example: month
     * @queryParam start_date date optional Y-m-d; required when preset=custom. Example: 2025-01-01
     * @queryParam end_date date optional Y-m-d; required when preset=custom. Example: 2025-01-31
     *
     * @response 202 {"job":{"id":"uuid","type":"csv","status":"queued","progress":0,"file_path":null,"download_url":null}}
     * @response 401 {"error":"Unauthenticated"}
     * @response 422 {"error":"Invalid query parameters.","errors":{"start_date":["The start date field must be a valid date."]}}
     *
     * @responseField job object The queued export job information with tracking details
     */
    public function exportPbacData(Request $request, PbacExportService $exportService)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->query();

        // Validate query parameters
        $validator = validator($validated, [
            'preset' => ['nullable', 'in:month,quarter,year,custom'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid query parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $job = $exportService->queueParticipantCsv($request, $participant->id);

        return response()->json(['job' => $job], 202);
    }

    /**
     * Queue chart export as PDF
     *
     * Queues a PDF export (chart image provided by the client) for the authenticated participant
     * and returns a tracking job payload.
     *
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
     *
     * @bodyParam chart_image string required Base64 image data URL (data:image/png;base64,...) Example: data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==
     * @bodyParam preset string optional One of: month, quarter, year, custom. Example: month
     * @bodyParam start_date date optional Y-m-d; used when preset=custom. Example: 2025-01-01
     * @bodyParam end_date date optional Y-m-d; used when preset=custom. Example: 2025-01-31
     *
     * @response 202 {"job":{"id":"uuid","type":"pdf","status":"queued","progress":0,"file_path":null,"download_url":null}}
     * @response 400 {"error":"Invalid image format"}
     * @response 401 {"error":"Unauthenticated"}
     * @response 422 {"message":"The given data was invalid.","errors":{"chart_image":["The chart image field is required."]}}
     *
     * @responseField job object The queued PDF export job information with tracking details
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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
     *
     * @response 200 {"job": {"id":"uuid","type":"csv","status":"processing","progress":25}}
     * @response 200 {"job": null}
     * @response 401 {"error":"Unauthenticated"}
     *
     * @responseField job object|null The active export job or null if no job is active
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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
     *
     * @urlParam jobId string required The export job UUID. Example: 3b0a3a80-5f8a-4a28-bb79-fd2c4b15c9ef
     *
     * @response 200 {"job": {"id":"uuid","type":"csv","status":"completed","progress":100,"file_path":"exports/participant/1/pbac_export_....csv"}}
     * @response 401 {"error":"Unauthenticated"}
     * @response 404 {"error":"Not found"}
     *
     * @responseField job object The export job with current status and progress information
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
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session (login via `/api/v1/participant/login` first)</b>
     *
     * @unauthenticated
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

    /**
     * Get participant profile
     *
     * Returns the authenticated participant's profile details.
     *
     * @authenticated
     *
     * @response 200 {"profile":{"registration_number":"participant123","enable_data_sharing":true,"opt_in_for_research":false,"medical_specialist_temporary_pin_expires_at":"2025-09-17T12:00:00.000000Z","medical_specialist_pin_expired":false,"created_at":"2025-09-17T11:00:00.000000Z"}}
     * @response 401 {"error":"Unauthenticated"}
     */
    public function profile(Request $request)
    {
        $participant = Auth::guard('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'profile' => [
                'registration_number' => $participant->registration_number,
                'enable_data_sharing' => (bool) $participant->enable_data_sharing,
                'opt_in_for_research' => (bool) $participant->opt_in_for_research,
                'medical_specialist_temporary_pin_expires_at' => $participant->medical_specialist_temporary_pin_expires_at,
                'medical_specialist_pin_expired' => $participant->isMedicalSpecialistPinExpired(),
                'created_at' => $participant->created_at,
            ],
        ]);
    }

    /**
     * Show dashboard web page
     */
    public function dashboardPage(ParticipantActivityLogService $logger)
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();

        if ($participant) {
            $logger->logDashboardVisit($participant);
            app(ParticipantSessionService::class)->heartbeat($participant, 'dashboard', true);
        }

        return view('participant.dashboard');
    }

    public function exportPage()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'export', true);
        }

        return view('participant.export-my-data');
    }

    public function dailyViewPage()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'daily-view', true);
        }

        return view('participant.daily-view');
    }

    public function education()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'education', true);
        }

        return view('participant.education');
    }

    public function selfManagement()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'self-management', true);
        }

        return view('participant.self-management');
    }

    public function externalLinks()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'external-links', true);
        }

        return view('participant.external-links');
    }

    public function generalInformation()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'general-information', true);
        }

        return view('participant.general-information');
    }

    public function settings()
    {
        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant-web')->user();
        if ($participant) {
            app(ParticipantSessionService::class)->heartbeat($participant, 'settings', true);
        }

        return view('participant.setting');
    }

    /**
     * Get videos for education page
     *
     * Returns all active videos for the education page ordered by their sequence.
     *
     * @response 200 {"videos":[{"id":1,"title":"Wat is de menstruele cyclus?","location":"education","order":1,"video_url":"https://youtube.com/shorts/YHxWLpAfY_M","video_type":"youtube","video_id":"YHxWLpAfY_M","thumbnail_url":"https://img.youtube.com/vi/YHxWLpAfY_M/hqdefault.jpg","embed_url":"https://www.youtube.com/embed/YHxWLpAfY_M","watch_url":"https://www.youtube.com/watch?v=YHxWLpAfY_M"}]}
     */
    public function getEducationVideos(VideoService $videoService)
    {
        $videos = $videoService->getVideosForLocation('education');

        return response()->json([
            'videos' => $videos,
        ]);
    }

    /**
     * Get videos for self-management page
     *
     * Returns all active videos for the self-management page ordered by their sequence.
     *
     * @response 200 {"videos":[{"id":13,"title":"Pijn verminderen door ontspanning","location":"self-management","order":1,"video_url":"https://youtube.com/shorts/oxTaiuHAq-U","video_type":"youtube","video_id":"oxTaiuHAq-U","thumbnail_url":"https://img.youtube.com/vi/oxTaiuHAq-U/hqdefault.jpg","embed_url":"https://www.youtube.com/embed/oxTaiuHAq-U","watch_url":"https://www.youtube.com/watch?v=oxTaiuHAq-U"}]}
     */
    public function getSelfManagementVideos(VideoService $videoService)
    {
        $videos = $videoService->getVideosForLocation('self-management');

        return response()->json([
            'videos' => $videos,
        ]);
    }

    /**
     * Get conditional videos for daily view
     *
     * Returns videos that meet the conditions based on the participant's data for a specific date.
     *
     * <b style="color: red;">AUTHENTICATION REQUIRED: Must have active session</b>
     *
     * @queryParam date date required Target date (Y-m-d). Example: 2025-09-10
     *
     * @response 200 {"videos":[{"id":3,"title":"Menstruatiepijn","location":"education","order":3,"video_url":"https://youtube.com/shorts/45GBEKxA9IQ","video_type":"youtube","video_id":"45GBEKxA9IQ","thumbnail_url":"https://img.youtube.com/vi/45GBEKxA9IQ/hqdefault.jpg","embed_url":"https://www.youtube.com/embed/45GBEKxA9IQ","watch_url":"https://www.youtube.com/watch?v=45GBEKxA9IQ"}]}
     * @response 401 {"error":"Unauthenticated"}
     * @response 422 {"error":"Invalid query parameters.","errors":{"date":["The date field is required."]}}
     */
    public function getDailyViewVideos(Request $request, VideoService $videoService)
    {
        $participant = auth('participant-web')->user();
        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->query();

        $validator = validator($validated, [
            'date' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid query parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $date = $validated['date'];

        $record = Pbac::query()
            ->forParticipant($participant->id)
            ->whereDate('reported_date', $date)
            ->orderBy('reported_date')
            ->first();

        if (! $record) {
            return response()->json(['videos' => []]);
        }

        $participantData = [
            'reported_date' => $record->reported_date,
            'pillars' => [
                'blood_loss' => $record->blood_loss,
                'pain' => $record->pain,
                'impact' => $record->impact,
                'general_health' => $record->general_health,
                'mood' => $record->mood,
                'stool_urine' => $record->stool_urine,
                'sleep' => $record->sleep,
                'diet' => $record->diet,
                'exercise' => $record->exercise,
                'sex' => $record->sex,
                'notes' => $record->notes,
            ],
            'sleep_hours' => $record->sleep['calculatedHours'] ?? null,
        ];

        $videos = $videoService->getVideosForDailyView($participantData);

        return response()->json([
            'videos' => $videos,
        ]);
    }

    /**
     * Dashboard Login (Bearer Token)
     *
     * Generates a temporary signed URL for auto-login to the participant web dashboard.
     * Requires a valid Bearer token in the Authorization header.
     *
     * @unauthenticated
     *
     * @header Authorization Bearer <token> required The API access token.
     *
     * @response 200 {"success": true, "message": "Login successful", "data": {"url": "http://example.com/participant/login?token=..."}}
     * @response 401 {"success": false, "message": "Unauthorized", "data": null}
     *
     * @responseField success boolean Whether the operation was successful
     * @responseField message string Status message
     * @responseField data object Contains the signed login URL
     * @responseField data.url string The temporary signed URL for web dashboard access
     */
    public function dashboardLogin(Request $request)
    {
        $bearerToken = $request->headers->get('authorization');
        $bearerToken = explode(' ', $bearerToken)[1] ?? null;

        if (! $bearerToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($bearerToken);

        if (! $accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }

        $user = $accessToken->tokenable;

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }

        $encodedToken = rtrim(strtr(base64_encode(Crypt::encryptString($bearerToken)), '+/', '-_'), '=');

        Cache::put('dashboard_login_token:'.hash('sha256', $bearerToken), true, now()->addMinutes((int) config('auth.dashboard_url_expiry', 5)));

        $url = URL::temporarySignedRoute(
            'participant.app.login',
            now()->addMinutes((int) config('auth.dashboard_url_expiry', 5)),
            ['token' => $encodedToken]
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'url' => $url,
            ],
        ]);
    }

    /**
     * @hideFromAPIDocumentation
     */
    public function refreshSession(Request $request)
    {
        if (! session('api_login')) {
            return response()->json([
                'success' => false,
                'message' => 'Not allowed',
            ], 403);
        }

        $token = session('api_auth_token');

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token missing',
            ], 401);
        }

        /** @var \App\Models\Participant|null $user */
        $user = Auth::guard('participant-web')->user();

        if ($user) {
            app(ParticipantSessionService::class)->heartbeat(
                $user,
                $request->input('section')
            );
        }

        $newExpiry = now()->addMinutes((int) config('auth.dashboard_url_expiry', 5));

        session()->put('api_login_expires_at', $newExpiry);

        return response()->json([
            'success' => true,
            'expires_at' => $newExpiry->timestamp,
        ]);
    }

    /**
     * @hideFromAPIDocumentation
     */
    public function heartbeat(Request $request)
    {
        /** @var \App\Models\Participant|null $user */
        $user = Auth::guard('participant-web')->user();

        if ($user) {
            app(ParticipantSessionService::class)->heartbeat(
                $user,
                $request->input('section')
            );
        }

        return response()->json(['success' => true]);
    }

    public function appLogin(Request $request)
    {

        try {

            $encoded = $request->query('token');
            $decoded = base64_decode(strtr($encoded, '-_', '+/'));
            $token = Crypt::decryptString($decoded);

        } catch (\Exception $e) {
            return view('participant.session_expired');
        }

        $expires = $request->query('expires');
        if ($token && $request->hasValidSignature()) {
            $cacheKey = 'dashboard_login_token:'.hash('sha256', $token);
            if (! Cache::has($cacheKey)) {
                return view('participant.session_expired');
            }

            $accessToken = PersonalAccessToken::findToken($token);
            if (! $accessToken) {
                return view('participant.session_expired');
            }

            $user = $accessToken->tokenable;

            if (! $user) {
                return view('participant.session_expired');
            }

            if (! $user instanceof Authenticatable) {
                return view('participant.session_expired');
            }

            Auth::guard('participant-web')->login($user);
            Cache::forget($cacheKey);
            $request->session()->put('api_login', true);
            $request->session()->put('api_login_expires_at', Carbon::createFromTimestamp($expires));
            $request->session()->put('api_auth_token', $token);

            return redirect('/participant/dashboard');
        }

        return view('participant.session_expired');
    }

    public function webLogin(Request $request)
    {
        if (Auth::guard('participant-web')->check()) {
            return redirect('/participant/dashboard');
        }

        return view('participant.web_login');
    }

    /**
     * Get Menstruation Wrapped data
     *
     * Returns a summary of the participant's last menstrual cycle symptoms.
     * Calculated from the previous "first day of period" until the day before the most recent one.
     *
     * <b style="color: red;">AUTHENTICATION REQUIRED:</b>
     * - Web: Logged-in session via `participant-web` guard
     * - Mobile: Bearer token (Sanctum Personal Access Token)
     *
     * @authenticated
     *
     * @header Authorization Bearer <token> required The API access token.
     *
     * @response 200 {
     *   "can_calculate": true,
     *   "start_date": "2025-08-01",
     *   "end_date": "2025-08-27",
     *   "cycle_length": 27,
     *   "blood_loss_days": 5,
     *   "spotting_days": 2,
     *   "pbac_score": 165,
     *   "show_pbac_high": true,
     *   "pain_days": 4,
     *   "extreme_pain_days": 1,
     *   "impact_days": 3
     * }
     * @response 200 {
     *   "can_calculate": false,
     *   "reason": "insufficient_data"
     * }
     * @response 200 {
     *   "can_calculate": false,
     *   "reason": "cycle_too_long",
     *   "cycle_length": 74
     * }
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     */
    public function getMenstruationWrapped(Request $request, PbacService $pbacService)
    {

        $participant = null;

        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());

            if ($accessToken && $accessToken->tokenable instanceof Authenticatable) {
                $participant = $accessToken->tokenable;
            }
        }

        if (! $participant && Auth::guard('participant-web')->check()) {
            $participant = Auth::guard('participant-web')->user();
        }

        if (! $participant) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $pbacService->getMenstruationWrappedData($participant->id);

        return response()->json($data);
    }

    /**
     * Get content for education page
     *
     * Fetches educational content from the CMS API based on specified filters.
     * Supports multiple content types including video, audio, text, and flipcard.
     * Content is filtered by language and optionally by content type.
     *
     * @response 200 {
     *   "content": [
     *     {
     *       "id": 42,
     *       "title": "Ademhalingsoefening bij Ernstige Pijn",
     *       "body": "<p>Gebruik deze oefening...</p>",
     *       "type": "video",
     *       "video_url": "https://cdn.ruby-cms.nl/videos/breathing-nl.mp4",
     *       "language": "nl",
     *       "filters_matched": {
     *           "pain": {
     *               "min": 7,
     *               "max": 10,
     *               "user_value": 8
     *           },
     *           "mood": {
     *               "min": 0,
     *               "max": 5,
     *               "user_value": 3
     *           }
     *       }
     *     },
     *     {
     *       "id": 2,
     *       "type": "audio",
     *       "title": "Meditation for Period Pain",
     *       "body": "Guided meditation audio...",
     *       "audio_url": "https://example.com/audio.mp3",
     *       "language": "nl",
     *       "filters_matched": {
     *           "pain": {
     *               "min": 5,
     *               "max": 10,
     *               "user_value": 7
     *           }
     *       }
     *     },
     *     {
     *       "id": 3,
     *       "type": "text",
     *       "title": "Nutrition Tips During Menstruation",
     *       "body": "<p>Important nutritional information...</p>",
     *       "language": "nl",
     *       "filters_matched": {
     *           "pain": {
     *               "min": 0,
     *               "max": 5,
     *               "user_value": 2
     *           }
     *       }
     *     },
     *     {
     *       "id": 4,
     *       "type": "flipcard",
     *       "title": "Myth vs Fact",
     *       "front_text": "You can't exercise during your period",
     *       "back_text": "Exercise can actually help reduce period symptoms",
     *       "language": "nl",
     *       "filters_matched": {
     *           "pain": {
     *               "min": 3,
     *               "max": 7,
     *               "user_value": 5
     *           }
     *       }
     *     }
     *   ]
     * }
     * @response 500 {
     *   "error": true,
     *   "message": "CMS API URL or API key is not configured"
     * }
     */
    public function fetchVideos(CmsApiCallService $cmsApiCallService, Request $request)
    // public function getEducationVideos(VideoService $videoService, Request $request)
    {
        // $videoService = new VideoService();
        // $videos = $videoService->getVideosForLocation('education');
        // return response()->json(['content' => $videos]);

        $response = $cmsApiCallService->call($request);

        if (array_key_exists('error', $response)) {
            return response()->json([
                'error' => $response['error'],
                'message' => $response['message'],
            ], 500);
        }

        return response()->json($response['data']);
    }

    /**
     * Get categories from CMS API
     *
     * Fetches available categories for content filtering from the CMS API.
     * Categories are localized based on the current session locale.
     *
     * @response 200 {
     *   "categories": [
     *     {
     *       "slug": "pain",
     *       "name": "Pijn",
     *       "description": "Lichamelijke pijn tracking",
     *       "value_type": "numeric",
     *       "metadata": {
     *         "min": 0,
     *         "max": 10
     *       }
     *     },
     *     {
     *       "slug": "mood",
     *       "name": "Stemming",
     *       "description": "Emotionele stemming tracking",
     *       "value_type": "numeric",
     *       "metadata": {
     *         "min": 0,
     *         "max": 10
     *       }
     *     }
     *   ],
     *   "total": 2,
     *   "language": "nl"
     * }
     * @response 500 {
     *   "error": true,
     *   "message": "Failed to fetch categories"
     * }
     */
    public function fetchCategories(Request $request)
    {
        $systemLang = session('locale') ?? app()->getLocale();
        $cmsApiUrl = config('cms.api_url');
        $cmsApiKey = config('cms.api_key');

        if (! $cmsApiUrl || ! $cmsApiKey) {
            return response()->json([
                'error' => true,
                'message' => 'CMS API URL or API key is not configured',
            ], 500);
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'accept' => 'application/json',
                'x-api-key' => $cmsApiKey,
            ])->get("$cmsApiUrl/api/v1/categories?lang=$systemLang");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Failed to fetch categories from CMS API',
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch categories: '.$e->getMessage(),
            ], 500);
        }
    }
}
