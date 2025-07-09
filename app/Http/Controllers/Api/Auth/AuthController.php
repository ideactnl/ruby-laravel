<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginLogsRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\LoginLog;
use App\Models\User;
use App\Services\LoginLogService;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Auth
 *
 * Authentication and user management endpoints.
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * Registers a user with a registration number, PIN, and agreement to share data for research.
     *
     * @bodyParam registration_number string required The unique registration number for the user. Example: user123
     * @bodyParam pin string required The PIN code for mobile login (min 6 chars). Example: 123456
     * @bodyParam opt_in_for_research boolean required Must be true to register. Example: true
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Registration successful",
     *   "data": {
     *     "user": { "id": 1, "registration_number": "user123", ... },
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
     * @responseField data object|null The user and access token or null
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $request->registerUser();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $token,
            ],
        ], 201);
    }

    /**
     * Login (PIN-based)
     *
     * Authenticates a user using registration number and PIN. Returns a Sanctum Bearer access_token for API access.
     *
     * @bodyParam registration_number string required The user's registration number. Example: user123
     * @bodyParam pin string required The user's PIN. Example: 123456
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful",
     *   "data": {
     *     "user": { ... },
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
     * @responseField data object|null The user and access token or null
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->attemptLogin();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'data' => null,
            ], 401);
        }

        (new LoginLogService)->log($user->registration_number);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $token,
            ],
        ]);

    }

    /**
     * Get login logs for a registration number.
     *
     * Returns login logs for the given registration number.
     *
     * @bodyParam registration_number string required The registration number. Example: user123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logs retrieved successfully.",
     *   "data": [
     *     {"id": 1, "registration_number": "user123", "login_at": "2025-07-02T11:00:00"}
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

        $user = User::where('registration_number', $registrationNumber)->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User with this registration number does not exist.',
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
     * Update user profile (toggles, password)
     *
     * Updates the authenticated user's profile toggles and/or password. Password change requires correct PIN.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @bodyParam enable_data_sharing boolean optional Enable or disable data sharing. Example: true
     * @bodyParam opt_in_for_research boolean optional Opt in or out of research participation. Example: false
     * @bodyParam password string optional New password (min 6 chars). Example: mysecurepassword
     * @bodyParam pin string optional The user's PIN (required if changing password). Example: 123456
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Profile updated successfully",
     *   "data": { "user": { ... } }
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
     * @responseField data object|null The updated user or null
     *
     * @authenticated
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $updated = false;

        if (array_key_exists('enable_data_sharing', $data)) {
            $user->enable_data_sharing = $data['enable_data_sharing'];
            $updated = true;
        }
        if (array_key_exists('opt_in_for_research', $data)) {
            $user->opt_in_for_research = $data['opt_in_for_research'];
            $updated = true;
        }
        if (! empty($data['password'])) {
            if (! Hash::check($data['pin'], $user->pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pin',
                    'data' => null,
                ], 403);
            }
            $user->password = Hash::make($data['password']);
            $updated = true;
        }
        if ($updated) {
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['user' => new UserResource($user)],
        ]);
    }

    /**
     * Logout (revoke token)
     *
     * Logs out the authenticated user by revoking the current Sanctum Bearer token.
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
}
