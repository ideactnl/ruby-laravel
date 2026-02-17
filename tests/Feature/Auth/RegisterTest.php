<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ResearchSurveyParticipant;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set up test database for second connection
    config(['database.connections.mysql_second' => [
        'driver' => 'sqlite',
        'database' => ':memory:'
    ]]);
    
    // Create the mga_survey_participant table for testing
    $schema = \DB::connection('mysql_second')->getSchemaBuilder();
    $schema->create(config('database.second_tables.participants'), function ($table) {
        $table->id();
        $table->string('study_number')->unique();
        $table->date('dob');
        $table->timestamps();
    });

    ResearchSurveyParticipant::create([
        'study_number' => 'STUDY121',
        'dob' => '1990-01-01',
    ]);
});

describe('Participant Registration', function () {
    /**
     * @test
     *
     * @covers AuthController::register
     * It should register a participant with valid data.
     */
    it('registers a participant with valid data', function () {
        $payload = [
            'registration_number' => 'testuser1',
            'pin' => '123456',
            'study_number' => 'STUDY121',
            'dob' => '1990-01-01',
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
                    'participant' => [
                        'id',
                        'registration_number',
                    ],
                    'access_token',
                ],
            ]);
    });

    /**
     * @test
     *
     * @covers AuthController::register
     * It should fail if pin is too short.
     */
    it('fails if pin is too short', function () {
        $payload = [
            'registration_number' => 'testuser2',
            'pin' => '123',
            'study_number' => 'STUDY121',
            'dob' => '1990-01-01',
            'opt_in_for_research' => true,
        ];

        $response = $this->postJson('/api/v1/register', $payload);
        $response->assertStatus(422);
    });

    /**
     * @test
     *
     * @covers AuthController::register
     * It should fail if opt_in_for_research is not a valid boolean.
     */
    it('fails if opt_in_for_research is not a valid boolean', function () {
        $payload = [
            'registration_number' => 'testuser3',
            'pin' => '123456',
            'study_number' => 'STUDY121',
            'dob' => '1990-01-01',
            'opt_in_for_research' => 'invalid_value',
        ];

        $response = $this->postJson('/api/v1/register', $payload);
        $response->assertStatus(422);
    });

    /**
     * @test
     *
     * @covers AuthController::register
     * It should allow registration with opt_in_for_research as false.
     */
    it('allows registration with opt_in_for_research as false', function () {
        $payload = [
            'registration_number' => 'testuser4',
            'pin' => '123456',
            'study_number' => 'STUDY121',
            'dob' => '1990-01-01',
            'opt_in_for_research' => false,
        ];

        $response = $this->postJson('/api/v1/register', $payload);
        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Registration successful',
            ]);
    });
});