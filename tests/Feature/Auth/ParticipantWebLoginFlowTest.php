<?php

use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

describe('Participant Web Dashboard Login Flow', function () {

    it('generates a signed url for dashboard login with valid bearer token', function () {
        $participant = Participant::factory()->create();
        $token = $participant->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson(route('participant.dashboard.login'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['url'],
            ]);

        $url = $response->json('data.url');
        expect($url)->toContain('/participant/app-login');
        expect($url)->toContain('signature=');
        expect($url)->toContain('token=');
    });

    it('fails to generate url without bearer token', function () {
        $response = $this->postJson(route('participant.dashboard.login'));

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    });

    it('logs in participant via signed url and redirects to dashboard', function () {
        $participant = Participant::factory()->create();
        $plainToken = $participant->createToken('test-token')->plainTextToken;

        Cache::put('dashboard_login_token:'.hash('sha256', $plainToken), true, now()->addMinutes((int) config('auth.dashboard_url_expiry', 5)));

        $encodedToken = rtrim(strtr(base64_encode(Crypt::encryptString($plainToken)), '+/', '-_'), '=');

        $url = URL::temporarySignedRoute(
            'participant.app.login',
            now()->addMinutes(5),
            ['token' => $encodedToken]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('participant.dashboard'));

        $this->assertAuthenticatedAs($participant, 'participant-web');
        $this->assertTrue(session('api_login'));
        $this->assertNotNull(session('api_login_expires_at'));
        $this->assertEquals($plainToken, session('api_auth_token'));
    });

    it('fails web login with invalid signature', function () {
        $participant = Participant::factory()->create();
        $plainToken = $participant->createToken('test-token')->plainTextToken;
        $encodedToken = rtrim(strtr(base64_encode(Crypt::encryptString($plainToken)), '+/', '-_'), '=');

        $url = route('participant.app.login', ['token' => $encodedToken]);

        $response = $this->get($url);
        $response->assertOk();
        $response->assertViewIs('participant.session_expired');
        $this->assertGuest('participant-web');
    });

    it('fails web login with tampered signature', function () {
        $participant = Participant::factory()->create();
        $plainToken = $participant->createToken('test-token')->plainTextToken;
        $encodedToken = rtrim(strtr(base64_encode(Crypt::encryptString($plainToken)), '+/', '-_'), '=');

        $validUrl = URL::temporarySignedRoute(
            'participant.app.login',
            now()->addMinutes(5),
            ['token' => $encodedToken]
        );

        $tamperedUrl = $validUrl.'tampered';

        $response = $this->get($tamperedUrl);

        $response->assertOk();
        $response->assertViewIs('participant.session_expired');
        $this->assertGuest('participant-web');
    });

    it('fails web login with tampered token payload', function () {
        $garbageToken = 'garbage_data';

        $url = URL::temporarySignedRoute(
            'participant.app.login',
            now()->addMinutes(5),
            ['token' => $garbageToken]
        );

        $response = $this->get($url);

        $response->assertOk();
        $response->assertViewIs('participant.session_expired');
        $this->assertGuest('participant-web');
    });

    it('allows access to dashboard with valid api login session', function () {
        $participant = Participant::factory()->create();

        $this->actingAs($participant, 'participant-web')
            ->withSession([
                'api_login' => true,
                'api_login_expires_at' => Carbon::now()->addMinutes(30),
            ])
            ->get(route('participant.dashboard'))
            ->assertOk();
    });

    it('logs out and redirects when api login session is expired', function () {
        $participant = Participant::factory()->create();

        $this->actingAs($participant, 'participant-web')
            ->withSession([
                'api_login' => true,
                'api_login_expires_at' => Carbon::now()->subMinutes(1), // Expired
            ])
            ->get(route('participant.dashboard'))
            ->assertRedirect(route('participant.web.login'));

        $this->assertGuest('participant-web');
    });

});
