<?php

use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

describe('User Registration', function () {
    /**
     * @test
     * @covers AuthController::register
     * It should register a user with valid data.
     */
    it('registers a user with valid data', function () {
        $payload = [
            'registration_number' => 'testuser1',
            'pin' => '123456',
            'opt_in_for_research' => true,
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'registration_number',
                    ],
                    'access_token',
                ],
            ]);
    });

    /**
     * @test
     * @covers AuthController::register
     * It should fail if pin is too short.
     */
    it('fails if pin is too short', function () {
        $payload = [
            'registration_number' => 'testuser2',
            'pin' => '123',
            'opt_in_for_research' => true,
        ];

        $response = $this->postJson('/api/v1/register', $payload);
        $response->assertStatus(422);
    });

    /**
     * @test
     * @covers AuthController::register
     * It should fail if opt_in_for_research is false.
     */
    it('fails if opt_in_for_research is false', function () {
        $payload = [
            'registration_number' => 'testuser3',
            'pin' => '123456',
            'opt_in_for_research' => false,
        ];

        $response = $this->postJson('/api/v1/register', $payload);
        $response->assertStatus(422);
    });
});
