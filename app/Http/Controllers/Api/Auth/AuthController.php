<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginLogsRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\MedicalSpecialistAccessRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\LoginLog;
use App\Models\Participant;
use App\Services\LoginLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Auth
 *
 * Authentication and participant management endpoints.
 */
class AuthController extends Controller
{
    /**
     * Register a new participant.
     *
     * Registers a participant with a registration number, PIN, and agreement to share data for research.
     *
     * @bodyParam registration_number string required The unique registration number for the participant. Example: participant123
     * @bodyParam pin string required The PIN code for mobile login (min 6 chars). Example: 123456
     * @bodyParam opt_in_for_research boolean required Must be true to register. Example: true
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Registration successful",
     *   "data": {
     *     "participant": { "id": 1, "registration_number": "participant123", ... },
     *     "access_token": "1|abcdef..."
     *   }
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Registration failed: <error message>",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data object|null The participant and access token or null
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $participant = $request->registerParticipant();
        $token = $participant->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'participant' => new ParticipantResource($participant),
                'access_token' => $token,
            ],
        ], 201);
    }

    /**
     * Login (PIN-based)
     *
     * Authenticates a participant using registration number and PIN. Returns a Sanctum Bearer access_token for API access.
     *
     * @bodyParam registration_number string required The participant's registration number. Example: participant123
     * @bodyParam pin string required The participant's PIN. Example: 123456
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful",
     *   "data": {
     *     "participant": { ... },
     *     "access_token": "1|abcdef..."
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid credentials",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data object|null The participant and access token or null
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $participant = $request->attemptLogin();
        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'data' => null,
            ], 401);
        }

        (new LoginLogService)->log($participant->registration_number);

        $token = $participant->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'participant' => new ParticipantResource($participant),
                'access_token' => $token,
            ],
        ]);

    }

    /**
     * Get login logs for a registration number.
     *
     * Returns login logs for the given registration number.
     *
     * @bodyParam registration_number string required The registration number. Example: participant123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logs retrieved successfully.",
     *   "data": [
     *     {"id": 1, "registration_number": "participant123", "login_at": "2025-07-02T11:00:00"}
     *   ]
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "No logs found for this registration number.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data array|null The login logs or null
     */
    public function loginLogs(LoginLogsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $registrationNumber = $data['registration_number'];

        $participant = Participant::where('registration_number', $registrationNumber)->first();
        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Participant with this registration number does not exist.',
                'data' => null,
            ], 404);
        }

        $logs = LoginLog::where('registration_number', $registrationNumber)->get();
        if ($logs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No logs found for this registration number.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logs retrieved successfully.',
            'data' => $logs,
        ]);

    }

    /**
     * Update participant profile (toggles, password)
     *
     * Updates the authenticated participant's profile toggles and/or password. Password change requires correct PIN.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @bodyParam enable_data_sharing boolean optional Enable or disable data sharing. Example: true
     * @bodyParam opt_in_for_research boolean optional Opt in or out of research participation. Example: false
     * @bodyParam password string optional New password (min 6 chars). Example: mysecurepassword
     * @bodyParam pin string optional The participant's PIN (required if changing password). Example: 123456
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Profile updated successfully",
     *   "data": { "participant": { ... } }
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Invalid pin",
     *   "data": null
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data object|null The updated participant or null
     *
     * @authenticated
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $participant = $request->user();
        $data = $request->validated();
        $updated = false;

        if (array_key_exists('enable_data_sharing', $data)) {
            $participant->enable_data_sharing = $data['enable_data_sharing'];
            $updated = true;
        }
        if (array_key_exists('opt_in_for_research', $data)) {
            $participant->opt_in_for_research = $data['opt_in_for_research'];
            $updated = true;
        }
        if (! empty($data['password'])) {
            if (! Hash::check($data['pin'], $participant->pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pin',
                    'data' => null,
                ], 403);
            }
            $participant->password = Hash::make($data['password']);
            $updated = true;
        }
        if ($updated) {
            $participant->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['participant' => new ParticipantResource($participant)],
        ]);
    }

    /**
     * Logout (revoke token)
     *
     * Logs out the authenticated participant by revoking the current Sanctum Bearer token.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logged out successfully",
     *   "data": null
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data null Always null for this endpoint
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'data' => null,
        ]);
    }

    /**
     * Enable or disable medical specialist access and set PIN.
     *
     * Allows a participant to enable specialist access by setting a numeric PIN (4-6 digits, valid for 7 days),
     * or disable access and remove the PIN.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     *
     * @bodyParam action string required Either 'enable' or 'disable'. Example: "enable"
     * @bodyParam pin string required when action is 'enable'. Numeric PIN (4-6 digits). Example: "1234"
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Medical specialist access enabled. PIN set successfully (Valid until: Aug 8, 2025 05:36)",
     *   "data": {
     *     "expires_at": "2025-08-08 05:36:00",
     *     "expires_at_formatted": "Aug 8, 2025 05:36"
     *   }
     * }
     * @response 200 {
     *   "success": true,
     *   "message": "Medical specialist access disabled",
     *   "data": null
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "pin": [
     *       "The pin must contain only numbers.",
     *       "The pin must be between 4 and 6 digits."
     *     ]
     *   }
     * }
     *
     * @authenticated
     */
    public function enableMedicalSpecialistAccess(MedicalSpecialistAccessRequest $request): JsonResponse
    {
        $participant = $request->user();

        if ($request->action === 'enable') {
            $expiryTime = $request->getExpiryTime();

            $participant->update([
                'allow_medical_specialist_login' => true,
                'medical_specialist_temporary_pin' => bcrypt($request->pin),
                'medical_specialist_temporary_pin_expires_at' => $expiryTime,
            ]);

            return response()->json([
                'success' => true,
                'message' => $request->getMessage(),
                'data' => [
                    'expires_at' => $expiryTime->toDateTimeString(),
                    'expires_at_formatted' => $expiryTime->format('M j, Y H:i'),
                ],
            ]);
        }

        $participant->update([
            'allow_medical_specialist_login' => false,
            'medical_specialist_temporary_pin' => null,
            'medical_specialist_temporary_pin_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->getMessage(),
            'data' => null,
        ]);
    }
}
