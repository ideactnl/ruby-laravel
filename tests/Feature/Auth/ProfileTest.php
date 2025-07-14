<?php

use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Profile Update API', function () {
    /**
     * @test
     *
     * @covers AuthController::updateProfile
     * It should update profile for authenticated participant.
     */
    it('updates profile for authenticated participant', function () {
        $participant = Participant::factory()->create(['registration_number' => 'profileparticipant']);
        $token = $participant->createToken('api')->plainTextToken;

        $payload = [
            'opt_in_for_research' => false,
            'pin' => '654321',
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/profile', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
    });

    /**
     * @test
     *
     * @covers AuthController::updateProfile
     * It should reject update when unauthenticated.
     */
    it('rejects update when unauthenticated', function () {
        $payload = [
            'opt_in_for_research' => false,
            'pin' => '654321',
        ];

        $response = $this->patchJson('/api/v1/profile', $payload);
        $response->assertUnauthorized();
    });
});
