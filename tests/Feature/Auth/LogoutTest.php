<?php

use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

describe('Logout API', function () {
    /**
     * @test
     *
     * @covers AuthController::logout
     * It should log out an authenticated participant.
     */
    it('logs out an authenticated participant', function () {
        $participant = Participant::factory()->create(['registration_number' => 'logoutparticipant']);
        Sanctum::actingAs($participant, ['*']);

        $response = $this->postJson('/api/v1/logout');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);
    });

    /**
     * @test
     *
     * @covers AuthController::logout
     * It should reject logout when unauthenticated.
     */
    it('rejects logout when unauthenticated', function () {
        $response = $this->postJson('/api/v1/logout');
        $response->assertUnauthorized();
    });
});
