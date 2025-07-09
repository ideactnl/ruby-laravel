<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Logout API', function () {
    /**
     * @test
     *
     * @covers AuthController::logout
     * It should log out an authenticated user.
     */
    it('logs out an authenticated user', function () {
        $user = User::factory()->create(['registration_number' => 'logoutuser']);
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/logout');

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
