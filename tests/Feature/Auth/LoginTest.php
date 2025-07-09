<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Login API', function () {
    /**
     * @test
     *
     * @covers AuthController::login
     * It should log in a user with valid credentials.
     */
    it('logs in a user with valid credentials', function () {
        $user = User::factory()->create(['registration_number' => 'testlogin']);

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
                'data' => ['user', 'access_token'],
            ]);
    });

    /**
     * @test
     *
     * @covers AuthController::login
     * It should fail login with invalid pin.
     */
    it('fails login with invalid pin', function () {
        $user = User::factory()->create(['registration_number' => 'testlogin2']);

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
