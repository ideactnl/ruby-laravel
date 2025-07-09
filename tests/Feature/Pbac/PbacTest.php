<?php

use App\Models\Pbac;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * @covers PBAC API Endpoints
 *
 * This suite tests all PBAC API endpoints, including creation, retrieval, filtering, and participant check.
 */
describe('PBAC API', function () {

    /**
     * @test
     *
     * @covers PBACController::index
     * It should return all PBAC records for an authenticated user.
     */
    it('retrieves all PBAC records for authenticated user', function () {
        $user = User::factory()->create();
        $pbac = Pbac::factory()->create([
            'user_id' => $user->id,
            'q3a' => 7,
            'q4a' => 2,
            'q3c' => 5,
            'reported_date' => '2025-07-01',
        ]);
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/pbac');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC records retrieved successfully.',
                'data' => [[
                    'ReportedDate' => '2025-07-01',
                    'BL' => 7,
                    'PadL' => 2,
                    'Mens' => 5,
                ]],
            ])
            ->assertJsonStructure([
                'data' => [[
                    'ReportedDate',
                    'BL',
                    'PadL',
                    'Mens',
                ]],
            ]);
    });

    it('returns 401 if not authenticated for index', function () {
        $response = $this->getJson('/api/v1/pbac');
        $response->assertUnauthorized();
    });

    /**
     * @test
     *
     * @covers PBACController::show
     * It should return a single PBAC record for an authenticated user.
     */
    it('shows a single PBAC record for authenticated user', function () {
        $user = User::factory()->create();
        $pbac = Pbac::factory()->create([
            'user_id' => $user->id,
            'q3a' => 8,
            'q4a' => 3,
            'q3c' => 6,
            'reported_date' => '2025-07-02',
        ]);
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/pbac/'.$pbac->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC record retrieved successfully.',
                'data' => [
                    'ReportedDate' => '2025-07-02',
                    'BL' => 8,
                    'PadL' => 3,
                    'Mens' => 6,
                ],
            ]);
    });

    /**
     * @test
     *
     * @covers PBACController::show
     * It should reject show if not authenticated.
     */
    it('returns 401 if not authenticated for show', function () {
        $pbac = Pbac::factory()->create();
        $response = $this->getJson('/api/v1/pbac/'.$pbac->id);
        $response->assertUnauthorized();
    });

    /**
     * @test
     *
     * @covers PBACController::filter
     * It should filter PBAC records by year for an authenticated user.
     */
    it('filters PBAC records by year', function () {
        $user = User::factory()->create();
        $pbac2024 = Pbac::factory()->create([
            'user_id' => $user->id,
            'reported_date' => '2024-07-01',
        ]);
        $pbac2025 = Pbac::factory()->create([
            'user_id' => $user->id,
            'reported_date' => '2025-07-01',
        ]);
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/pbac/filter?year=2025');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC records retrieved successfully.',
            ])
            ->assertJsonFragment(['ReportedDate' => '2025-07-01'])
            ->assertJsonMissing(['ReportedDate' => '2024-07-01']);
    });

    /**
     * @test
     *
     * @covers PBACController::store
     * It should create a PBAC record for an authenticated user with multiple question codes.
     */
    it('creates a PBAC record for authenticated user', function () {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;
        $payload = [
            'ReportedDate' => '2025-07-01',
            'BL' => 2,
            'PadL' => 1,
            'Mens' => 5,
            'Pain' => 4,
            'PainL1' => 2,
            'PainL2' => 3,
            'PainL3' => 1,
            'PainTrig1' => 1,
            'PainBig' => 2,
            'work' => 1,
            'school' => 0,
            'QoL' => 8,
        ];
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/pbac', $payload);
        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC record created successfully.',
                'data' => [
                    'ReportedDate' => '2025-07-01',
                    'BL' => 2,
                    'PadL' => 1,
                    'Mens' => 5,
                    'Pain' => 4,
                    'PainL1' => 2,
                    'PainL2' => 3,
                    'PainL3' => 1,
                    'PainTrig1' => 1,
                    'PainBig' => 2,
                    'work' => 1,
                    'school' => 0,
                    'QoL' => 8,
                ],
            ]);
    });

    /**
     * @test
     *
     * @covers PBACController::store
     * It should reject PBAC creation if the user is not authenticated.
     */
    it('returns 401 if not authenticated for store', function () {
        $payload = [
            'ReportedDate' => '2025-07-01',
        ];
        $response = $this->postJson('/api/v1/pbac', $payload);
        $response->assertUnauthorized();
        $response->assertUnauthorized();
    });

    /**
     * @test
     *
     * @covers PBACController::check
     * It should confirm the existence of a participant for an authenticated user.
     */
    it('checks participant existence', function () {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/pbac/check');
        $response->assertOk();
    });

    /**
     * @test
     *
     * @covers PBACController::check
     * It should reject participant check if not authenticated.
     */
    it('returns 401 if not authenticated for check', function () {
        $response = $this->getJson('/api/v1/pbac/check');
        $response->assertUnauthorized();
    });
});
