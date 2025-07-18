<?php

namespace App\Http\Controllers\Api\Participant;

use App\Http\Controllers\Controller;
use App\Models\Participant;
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
     * Show the participant login form (Blade view).
     *
     * If already authenticated, redirects to dashboard.
     * Route: GET /participant/web-login (web.php)
     */
    public function showLoginForm()
    {
        if (Auth::guard('participant-web')->check()) {
            return redirect('/participant/dashboard');
        }

        return view('participant.web_login');
    }
}
