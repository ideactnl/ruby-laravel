<?php

use App\Models\LoginLog;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Login Logs API', function () {

    /**
     * @test
     *
     * @covers AuthController::loginLogs
     * It should retrieve login logs for authenticated participant.
     */
    it('retrieves login logs for authenticated participant', function () {
        $participant = Participant::factory()->create(['registration_number' => 'logsparticipant']);

        LoginLog::factory()->create([
            'registration_number' => $participant->registration_number,
        ]);

        $payload = [
            'registration_number' => $participant->registration_number,
        ];
        $response = $this->postJson('/api/v1/login-logs', $payload);

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
