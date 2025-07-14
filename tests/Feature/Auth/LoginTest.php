<?php

use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Login API', function () {
    /**
     * @test
     *
     * @covers AuthController::login
     * It should log in a participant with valid credentials.
     */
    it('logs in a participant with valid credentials', function () {
        $participant = Participant::factory()->create([
            'registration_number' => 'testlogin',
            'pin' => Hash::make('123456'), 
        ]);

        $payload = [
            'registration_number' => 'testlogin',
            'pin' => '123456',
        ];

        $response = $this->postJson('/api/v1/login', $payload);
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'data' => ['participant', 'access_token'],
            ]);
    });

    /**
     * @test
     *
     * @covers AuthController::login
     * It should fail login with invalid pin.
     */
    it('fails login with invalid pin', function () {
        $participant = Participant::factory()->create(['registration_number' => 'testlogin2']);

        $payload = [
            'registration_number' => 'testlogin2',
            'pin' => 'wrongpin',
        ];

        $response = $this->postJson('/api/v1/login', $payload);
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    });
});
