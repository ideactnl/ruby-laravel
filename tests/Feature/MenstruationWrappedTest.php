<?php

use App\Models\Participant;
use App\Models\Pbac;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->participant = Participant::factory()->create();
    $this->actingAs($this->participant, 'participant-web');
});

describe('Menstruation Wrapped API', function () {
    it('returns insufficient_data when less than 2 cycles exist', function () {
        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => Carbon::now(),
            'is_bl_first_day_period' => true,
        ]);

        $response = $this->getJson('/api/v1/participant/menstruation-wrapped');

        $response->assertStatus(200)
            ->assertJson([
                'can_calculate' => false,
                'reason' => 'insufficient_data',
            ]);
    });

    it('calculates metrics correctly for a valid cycle', function () {
        $recentStart = Carbon::now()->startOfDay();
        $previousStart = Carbon::now()->subDays(28)->startOfDay();

        // Previous cycle start
        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => $previousStart,
            'is_bl_first_day_period' => true,
            'menstrual_blood_loss' => 1,
            'pain_slider_value' => 3,
            'is_impact_missed_work' => false,
            'is_impact_missed_school' => false,
            'is_impact_could_not_sport' => false,
            'is_impact_missed_special_activities' => false,
            'is_impact_missed_leisure_activities' => false,
            'is_impact_had_to_sit_more' => false,
            'is_impact_could_not_move' => false,
            'is_impact_had_to_stay_longer_in_bed' => false,
            'is_impact_could_not_do_unpaid_work' => false,
            'is_impact_other' => false,
            'bl_pad_small' => 0,
            'bl_pad_medium' => 2,
            'bl_pad_large' => 0,
            'bl_tampon_small' => 0,
            'bl_tampon_medium' => 0,
            'bl_tampon_large' => 0,
        ]);

        // Middle day
        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => $previousStart->copy()->addDays(2),
            'is_bl_first_day_period' => false,
            'menstrual_blood_loss' => 1,
            'pain_slider_value' => 6,
            'is_impact_missed_work' => true,
            'is_impact_missed_school' => false,
            'is_impact_could_not_sport' => false,
            'is_impact_missed_special_activities' => false,
            'is_impact_missed_leisure_activities' => false,
            'is_impact_had_to_sit_more' => false,
            'is_impact_could_not_move' => false,
            'is_impact_had_to_stay_longer_in_bed' => false,
            'is_impact_could_not_do_unpaid_work' => false,
            'is_impact_other' => false,
            'bl_pad_small' => 0,
            'bl_pad_medium' => 0,
            'bl_pad_large' => 0,
            'bl_tampon_small' => 0,
            'bl_tampon_medium' => 0,
            'bl_tampon_large' => 1, 
        ]);

        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => $recentStart,
            'is_bl_first_day_period' => true,
        ]);

        $response = $this->getJson('/api/v1/participant/menstruation-wrapped');

        $response->assertStatus(200)
            ->assertJson([
                'can_calculate' => true,
                'start_date' => $previousStart->format('Y-m-d'),
                'end_date' => $recentStart->copy()->subDay()->format('Y-m-d'),
                'cycle_length' => 28,
                'blood_loss_days' => 2,
                'pbac_score' => 30,
                'pain_days' => 2,
                'extreme_pain_days' => 1,
                'impact_days' => 1,
                'total_tracked_days' => 3,
            ]);

        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => $previousStart->copy()->subDays(10),
            'is_diet_answered' => true,
        ]);

        $response = $this->getJson('/api/v1/participant/menstruation-wrapped');
        $response->assertJsonPath('total_tracked_days', 4);
    });

    it('rejects cycle longer than 60 days', function () {
        $recentStart = Carbon::now();
        $previousStart = Carbon::now()->subDays(65);

        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => $previousStart,
            'is_bl_first_day_period' => true,
        ]);

        Pbac::factory()->create([
            'participant_id' => $this->participant->id,
            'reported_date' => $recentStart,
            'is_bl_first_day_period' => true,
        ]);

        $response = $this->getJson('/api/v1/participant/menstruation-wrapped');

        $response->assertStatus(200)
            ->assertJson([
                'can_calculate' => false,
                'reason' => 'cycle_too_long',
            ]);
    });
    
    it('authenticates successfully via Sanctum bearer token', function () {
        $this->flushSession();
        Auth::guard('participant-web')->logout();
        
        $participant = Participant::factory()->create();
        $token = $participant->createToken('test-token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/participant/menstruation-wrapped');
        
        $response->assertStatus(200)
            ->assertJson([
                'can_calculate' => false,
                'reason' => 'insufficient_data',
            ]);
    });
});
