<?php

use App\Models\Participant;
use App\Models\Pbac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * @covers PBAC API Endpoints (v2)
 *
 * This suite tests all PBAC API endpoints (v2/mobile schema), including creation, retrieval,
 * filtering, and participant check.
 */
describe('PBAC API', function () {

    /**
     * @test
     *
     * It should return all PBAC records for an authenticated participant.
     */
    it('retrieves all PBAC records for authenticated participant', function () {
        $participant = Participant::factory()->create();
        $pbac = Pbac::factory()->create([
            'participant_id' => $participant->id,
            'reported_date' => '2025-07-01',
            'bl_pad_large' => 2,
            'spotting' => false,
        ]);
        Sanctum::actingAs($participant, ['*']);

        $response = $this->getJson('/api/v1/pbac');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC records retrieved successfully.',
                'data' => [[
                    'reportedDate' => '2025-07-01',
                ]],
            ])
            ->assertJsonStructure([
                'data' => [[
                    'reportedDate',
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
     * It should return a single PBAC record for an authenticated participant.
     */
    it('shows a single PBAC record for authenticated participant', function () {
        $participant = Participant::factory()->create();
        $pbac = Pbac::factory()->create([
            'participant_id' => $participant->id,
            'reported_date' => '2025-07-02',
            'bl_pad_large' => 3,
            'spotting' => true,
        ]);
        Sanctum::actingAs($participant, ['*']);

        $response = $this->getJson('/api/v1/pbac/'.$pbac->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC record retrieved successfully.',
                'data' => [
                    'reportedDate' => '2025-07-02',
                ],
            ]);
    });

    /**
     * @test
     *
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
     * It should filter PBAC records by year for an authenticated participant.
     */
    it('filters PBAC records by year', function () {
        $participant = Participant::factory()->create();
        $pbac2024 = Pbac::factory()->create([
            'participant_id' => $participant->id,
            'reported_date' => '2024-07-01',
        ]);
        $pbac2025 = Pbac::factory()->create([
            'participant_id' => $participant->id,
            'reported_date' => '2025-07-01',
        ]);
        Sanctum::actingAs($participant, ['*']);

        $response = $this->getJson('/api/v1/pbac/filter?year=2025');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC records retrieved successfully.',
            ])
            ->assertJsonFragment(['reportedDate' => '2025-07-01'])
            ->assertJsonMissing(['reportedDate' => '2024-07-01']);
    });

    /**
     * @test
     *
     * It should create a PBAC record for an authenticated participant with multiple question codes.
     */
    it('creates a PBAC record for authenticated participant', function () {
        $participant = Participant::factory()->create();
        Sanctum::actingAs($participant, ['*']);
        $payload = [
            'reportedDate' => '2025-07-01',
            'isBloodLossAnswered' => 1,
            'blPadLarge' => 1,
            'blPadMedium' => 2,
            'blPadSmall' => 0,
            'isPainAnswered' => 1,
            'painSliderValue' => 4,
            'isImpactAnswered' => 1,
            'impactSliderGradeYourDay' => 8,
        ];
        $response = $this->postJson('/api/v1/pbac', $payload);
        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'PBAC record created successfully.',
                'data' => [
                    'reportedDate' => '2025-07-01',
                    'bloodLoss' => [
                        'answered' => true,
                        'amount' => 3,
                    ],
                    'pain' => [
                        'answered' => true,
                        'value' => 4,
                    ],
                    'impact' => [
                        'answered' => true,
                        'gradeYourDay' => 8,
                    ],
                ],
            ]);
    });

    /**
     * @test
     *
     * It should reject PBAC creation if the participant is not authenticated.
     */
    it('returns 401 if not authenticated for store', function () {
        $payload = ['reportedDate' => '2025-07-01'];
        $response = $this->postJson('/api/v1/pbac', $payload);
        $response->assertUnauthorized();
    });

    /**
     * @test
     *
     * It should confirm the existence of a participant for an authenticated participant.
     */
    it('checks participant existence', function () {
        $participant = Participant::factory()->create();
        Sanctum::actingAs($participant, ['*']);
        $response = $this->getJson('/api/v1/pbac/check');
        $response->assertOk();
    });

    /**
     * @test
     *
     * It should reject participant check if not authenticated.
     */
    it('returns 401 if not authenticated for check', function () {
        $response = $this->getJson('/api/v1/pbac/check');
        $response->assertUnauthorized();
    });
});
