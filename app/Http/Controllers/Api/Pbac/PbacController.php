<?php

namespace App\Http\Controllers\Api\Pbac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pbac\StoreOrUpdatePbacRequest;
use App\Http\Resources\PbacResource;
use App\Models\Pbac;
use Illuminate\Http\Request;

class PbacController extends Controller
{
    /**
     * Retrieve all PBAC records for the authenticated participant.
     *
     * Returns entries using the mobile-style field names (camelCase). Computed fields like
     * pbacScorePerDay are included for convenience and are derived from stored data.
     *
     * @group PBAC
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC records retrieved successfully.",
     *   "data": [
     *     {
     *       "reportedDate": "2025-09-15",
     *       "isBloodLossAnswered": 1,
     *       "blPadSmall": 2,
     *       "painSliderValue": 5,
     *       "pbacScorePerDay": 2
     *     }
     *   ]
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

        $pbacs = Pbac::forParticipant($participant->id)->orderBy('reported_date')->get();

        return response()->json([
            'success' => true,
            'message' => 'PBAC records retrieved successfully.',
            'data' => PbacResource::collection($pbacs),
        ]);
    }

    /**
     * Check if the authenticated participant exists.
     *
     * Quick identity/auth check endpoint for the mobile app. Returns the currently authenticated
     * participant as the source of truth.
     *
     * @group PBAC
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Participant found.",
     *   "participant": { "id": 1, "registration_number": "12345" }
     * }
     * @response 401 {
     *   "message": "Authentication required. Please provide a valid Bearer token.",
     *   "participant": null
     * }
     * @response 404 {
     *   "message": "Participant not found.",
     *   "participant": null
     * }
     * @response 500 {
     *   "message": "<error message>",
     *   "participant": null
     * }
     *
     * @responseField message string A human-readable message
     * @responseField participant object|null The participant object or null
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

        // Authenticated via Sanctum; participant is present
        return response()->json([
            'message' => 'Participant found.',
            'participant' => $participant,
        ]);
    }

    /**
     * Create or update a PBAC record for the authenticated participant.
     *
     * Provide mobile-style fields (see body parameters on this endpoint in the docs). If an entry
     * for the given reportedDate exists, it will be updated; otherwise, it will be created.
     *
     * @group PBAC
     *
     * @authenticated
     *
     * @response 201 {
     *   "success": true,
     *   "message": "PBAC record created successfully.",
     *   "data": { "reportedDate": "2025-09-15" }
     * }
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC record updated successfully.",
     *   "data": { "reportedDate": "2025-09-15" }
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

        try {
            $validated = $request->validated();

            $reportedDate = $validated['reportedDate'];
            $data = Pbac::camelToSnake($validated);
            $data['reported_date'] = $reportedDate;
            unset($data['reportedDate']);

            $pbac = Pbac::firstOrNew([
                'participant_id' => $participant->id,
                'reported_date' => $reportedDate,
            ]);
            $created = ! $pbac->exists;
            $pbac->fill($data);
            $pbac->participant_id = $participant->id;
            $pbac->save();

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
     * @group PBAC
     *
     * @urlParam id integer required The PBAC record ID. Example: 1
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC record retrieved successfully.",
     *   "data": { "reportedDate": "2025-09-15" }
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

        $pbac = Pbac::forParticipant($participant->id)->where('id', $id)->first();
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
     * @group PBAC
     *
     * @queryParam day integer optional The day to filter. Example: 3
     * @queryParam month integer optional The month to filter. Example: 9
     * @queryParam year integer optional The year to filter. Example: 2025
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "PBAC records retrieved successfully.",
     *   "data": []
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

        $q = Pbac::forParticipant($participant->id);
        if (! empty($validated['year'])) {
            $q->whereYear('reported_date', $validated['year']);
        }
        if (! empty($validated['month'])) {
            $q->whereMonth('reported_date', $validated['month']);
        }
        if (! empty($validated['day'])) {
            $q->whereDay('reported_date', $validated['day']);
        }

        $pbacs = $q->orderBy('reported_date')->get();

        return response()->json([
            'success' => true,
            'message' => 'PBAC records retrieved successfully.',
            'data' => PbacResource::collection($pbacs),
        ]);
    }
}
