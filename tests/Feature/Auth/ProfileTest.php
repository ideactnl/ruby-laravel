<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

describe('Profile Update API', function () {
    /**
     * @test
     * @covers AuthController::updateProfile
     * It should update profile for authenticated user.
     */
    it('updates profile for authenticated user', function () {
        $user = User::factory()->create(['registration_number' => 'profileuser']);
        $token = $user->createToken('api')->plainTextToken;

        $payload = [
            'opt_in_for_research' => false,
            'pin' => '654321',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/v1/profile', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
    });

    /**
     * @test
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
