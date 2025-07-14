<?php

namespace App\Http\Controllers\Api\Pbac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pbac\StoreOrUpdatePbacRequest;
use App\Http\Resources\PbacResource;
use App\Services\PbacService;
use Illuminate\Http\Request;

/**
 *@group PBAC
 * Endpoints related to PBAC records and participant management.
 */
class PbacController extends Controller
{
    protected $pbacService;

    public function __construct(PbacService $pbacService)
    {
        $this->pbacService = $pbacService;
    }

    /**
     * Retrieve all PBAC records for the authenticated participant.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC records retrieved successfully.",
     *   "data": [ { "ReportedDate": "2025-07-01", ... } ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Authentication required. Please provide a valid Bearer token.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data array|null The list of PBAC records or null
     *
     * @authenticated
     */
    public function index(Request $request)
    {
        $participant = $request->user();
        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please provide a valid Bearer token.',
                'data' => null,
            ], 401);
        }
        $pbacs = $this->pbacService->getParticipantPbacs($participant->registration_number);

        return response()->json([
            'success' => true,
            'message' => 'PBAC records retrieved successfully.',
            'data' => PbacResource::collection($pbacs),
        ]);

    }

    /**
     * Create or update a PBAC record for the authenticated participant.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @response 201 {
     *   "success": true,
     *   "message": "PBAC record created successfully.",
     *   "data": { "ReportedDate": "2025-07-01", ... }
     * }
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC record updated successfully.",
     *   "data": { "ReportedDate": "2025-07-01", ... }
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Failed to save PBAC record: <error message>",
     *   "data": null
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Authentication required. Please provide a valid Bearer token.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data object|null The created or updated PBAC record or null
     *
     * @authenticated
     */
    public function store(StoreOrUpdatePbacRequest $request)
    {
        $participant = $request->user();
        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please provide a valid Bearer token.',
                'data' => null,
            ], 401);
        }
        $data = $request->validated();
        $data['registration_number'] = $participant->registration_number;
        try {
            [$pbac, $created] = $this->pbacService->upsertByRegistrationAndDate($data);

            return response()->json([
                'success' => true,
                'message' => $created ? 'PBAC record created successfully.' : 'PBAC record updated successfully.',
                'data' => new PbacResource($pbac),
            ], $created ? 201 : 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save PBAC record: '.$e->getMessage(),
                'data' => null,
            ], 400);
        }
    }

    /**
     * Retrieve a single PBAC record by its ID for the authenticated participant.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @urlParam id integer required The PBAC record ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC record retrieved successfully.",
     *   "data": { "ReportedDate": "2025-07-01", ... }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Authentication required. Please provide a valid Bearer token.",
     *   "data": null
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "PBAC record not found or access denied.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data object|null The PBAC record or null
     *
     * @authenticated
     */
    public function show($id, Request $request)
    {
        $participant = $request->user();
        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please provide a valid Bearer token.',
                'data' => null,
            ], 401);
        }
        $pbac = $this->pbacService->getParticipantPbacs($participant->registration_number, $id);
        if (! $pbac) {
            return response()->json([
                'success' => false,
                'message' => 'PBAC record not found or access denied.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'PBAC record retrieved successfully.',
            'data' => new PbacResource($pbac),
        ]);

    }

    /**
     * Filter PBAC records for the authenticated participant by day, month, and/or year.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @queryParam day integer optional The day to filter. Example: 3
     * @queryParam month integer optional The month to filter. Example: 7
     * @queryParam year integer optional The year to filter. Example: 2025
     *
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC records retrieved successfully.",
     *   "data": [ { "ReportedDate": "2025-07-01", ... } ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Authentication required. Please provide a valid Bearer token.",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data array|null The list of PBAC records or null
     *
     * @authenticated
     */
    public function filter(Request $request)
    {
        $participant = $request->user();

        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please provide a valid Bearer token.',
                'data' => null,
            ], 401);
        }
        $validated = $request->validate([
            'day' => 'nullable|integer',
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
        ]);
        $pbacs = $this->pbacService->getParticipantPbacs(
            $participant->registration_number,
            null,
            $validated['day'] ?? null,
            $validated['month'] ?? null,
            $validated['year'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'PBAC records retrieved successfully.',
            'data' => PbacResource::collection($pbacs),
        ]);

    }

    /**
     * Check if the authenticated participant exists.
     *
     * **Requires authentication via Bearer token in the Authorization header.**
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Participant found.",
     *   "data": { "id": 1, "registration_number": "12345", ... }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Authentication required. Please provide a valid Bearer token.",
     *   "data": null
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Participant not found.",
     *   "data": null
     * }
     * @response 500 {
     *   "success": false,
     *   "message": "<error message>",
     *   "data": null
     * }
     *
     * @responseField success boolean Whether the request was successful
     * @responseField message string A human-readable message
     * @responseField data object|null The participant object or null
     *
     * @authenticated
     */
    public function check(Request $request)
    {
        $participant = $request->user();
        if (! $participant) {
            return response()->json([
                'message' => 'Authentication required. Please provide a valid Bearer token.',
                'participant' => null,
            ], 401);
        }
        try {
            $foundParticipant = $this->pbacService->checkParticipant($participant->registration_number);
            if ($foundParticipant) {
                return response()->json([
                    'message' => 'Participant found.',
                    'participant' => $foundParticipant,
                ]);
            } else {
                return response()->json([
                    'message' => 'Participant not found.',
                    'participant' => null,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'participant' => null,
            ], 500);
        }
    }
}
