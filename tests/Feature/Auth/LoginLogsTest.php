<?php

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Login Logs API', function () {

    /**
     * @test
     *
     * @covers AuthController::loginLogs
     * It should retrieve login logs for authenticated user.
     */
    it('retrieves login logs for authenticated user', function () {
        $user = User::factory()->create(['registration_number' => 'logsuser']);
        $token = $user->createToken('api')->plainTextToken;

        LoginLog::factory()->create([
            'registration_number' => $user->registration_number,
        ]);

        $payload = [
            'registration_number' => $user->registration_number,
        ];
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/login-logs', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [],
            ]);
    });

    it('rejects login logs when unauthenticated', function () {
        $payload = [
            'registration_number' => 'nonexistent',
        ];
        $response = $this->postJson('/api/v1/login-logs', $payload);
        $response->assertStatus(404);
    });
});
