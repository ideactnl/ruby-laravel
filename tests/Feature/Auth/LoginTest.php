<?php

use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Login API', function () {
    /**
     * @test
     *
     * It should log in a participant using SPA (session/cookie) auth and access dashboard.
     */
    it('logs in a participant via web-login and can access dashboard (SPA session)', function () {
        $participant = Participant::factory()->create([
            'registration_number' => 'spauser',
            'password' => bcrypt('webpassword'),
        ]);

        $this->get('/sanctum/csrf-cookie');

        $headers = [
            'X-Requested-With' => 'XMLHttpRequest',
            'Origin' => config('app.url'),
        ];

        $session = ['_token' => csrf_token()];

        $response = $this->withSession($session)
            ->postJson('/api/v1/participant/login', [
                'registration_number' => 'spauser',
                'password' => 'webpassword',
            ], $headers);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'participant' => [
                    'id' => $participant->id,
                    'registration_number' => 'spauser',
                ],
            ]);

        $dashboard = $this->withSession($session)
            ->withCookie(session_name(), session()->getId())
            ->get('/api/v1/participant/dashboard');
        $dashboard->assertOk();
    });

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
