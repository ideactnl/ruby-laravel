<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\Pbac;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MenstruationWrappedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createOptimalUser();
        $this->createHeavyUser();
        $this->createPainUser();
        $this->createImpactUser();
        $this->createSpottingUser();
        $this->createLongCycleUser();
        $this->createMinimalDataUser();

        $this->command->info('Menstruation Wrapped test data generated successfully.');
        $this->command->info('Test Users: wrapped_optimal, wrapped_heavy, wrapped_pain, wrapped_impact, wrapped_spotting, wrapped_long, wrapped_minimal');
        $this->command->info('Password: "password", PIN: "123456"');
    }

    private function createParticipant(string $regNum)
    {
        $participant = Participant::updateOrCreate(
            ['registration_number' => $regNum],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => true,
            ]
        );
        Pbac::where('participant_id', $participant->id)->delete();
        return $participant;
    }

    private function createOptimalUser()
    {
        $p = $this->createParticipant('wrapped_optimal');
        $today = Carbon::now();
        
        // Cycle: Today-28 to Today-1. Current period starts Today.
        $this->addPeriodStart($p->id, $today);
        $this->addPeriodStart($p->id, $today->copy()->subDays(28));
        
        // Bleeding (Day 1-4)
        $this->addBleeding($p->id, $today->copy()->subDays(28), 2, 2); // Moderate
        $this->addBleeding($p->id, $today->copy()->subDays(27), 1, 1);
        $this->addBleeding($p->id, $today->copy()->subDays(26), 0, 0, 1); // Spotting
        
        // Mild Pain
        $this->addPain($p->id, $today->copy()->subDays(28), 3); 
    }

    private function createHeavyUser()
    {
        $p = $this->createParticipant('wrapped_heavy');
        $today = Carbon::now();
        $start = $today->copy()->subDays(30);
        
        $this->addPeriodStart($p->id, $today);
        $this->addPeriodStart($p->id, $start);
        
        // Trigger PBAC > 150
        // Day 1: 5 Large Pads (100)
        $this->addCustomPbac($p->id, $start, [
            'menstrual_blood_loss' => 1,
            'bl_pad_large' => 5,
            'is_blood_loss_answered' => true
        ]);
        // Day 2: 3 Large Tampons (60) -> Total 160
        $this->addCustomPbac($p->id, $start->copy()->addDay(), [
            'menstrual_blood_loss' => 1,
            'bl_tampon_large' => 3,
            'is_blood_loss_answered' => true
        ]);
    }

    private function createPainUser()
    {
        $p = $this->createParticipant('wrapped_pain');
        $today = Carbon::now();
        $start = $today->copy()->subDays(28);
        
        $this->addPeriodStart($p->id, $today);
        $this->addPeriodStart($p->id, $start);
        
        // Extreme Pain (> 5) for 4 days
        for($i=0; $i<4; $i++) {
            $this->addPain($p->id, $start->copy()->addDays($i), 7);
        }
    }

    private function createImpactUser()
    {
        $p = $this->createParticipant('wrapped_impact');
        $today = Carbon::now();
        $start = $today->copy()->subDays(28);
        
        $this->addPeriodStart($p->id, $today);
        $this->addPeriodStart($p->id, $start);
        
        // Impact on multiple days
        $this->addCustomPbac($p->id, $start, ['is_impact_missed_work' => true]);
        $this->addCustomPbac($p->id, $start->copy()->addDay(), ['is_impact_missed_school' => true]);
        $this->addCustomPbac($p->id, $start->copy()->addDays(2), ['is_impact_could_not_sport' => true]);
        $this->addCustomPbac($p->id, $start->copy()->addDays(3), ['is_impact_had_to_stay_longer_in_bed' => true]);
    }

    private function createSpottingUser()
    {
        $p = $this->createParticipant('wrapped_spotting');
        $today = Carbon::now();
        $start = $today->copy()->subDays(25);
        
        $this->addPeriodStart($p->id, $today);
        $this->addPeriodStart($p->id, $start);
        
        // Only spotting, no menstrual_blood_loss=1
        for($i=0; $i<5; $i++) {
            $this->addCustomPbac($p->id, $start->copy()->addDays($i), [
                'spotting' => true,
                'is_blood_loss_answered' => true,
                'menstrual_blood_loss' => 0
            ]);
        }
    }

    private function createLongCycleUser()
    {
        $p = $this->createParticipant('wrapped_long');
        $today = Carbon::now();
        
        $this->addPeriodStart($p->id, $today);
        $this->addPeriodStart($p->id, $today->copy()->subDays(65)); // > 60 days
    }

    private function createMinimalDataUser()
    {
        $p = $this->createParticipant('wrapped_minimal');
        $today = Carbon::now();
        
        // ONLY ONE START DATE -> Insufficient data
        $this->addPeriodStart($p->id, $today->copy()->subDays(5));
    }

    // Helpers
    private function addPeriodStart($pid, $date) {
        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            ['is_bl_first_day_period' => true]
        );
    }

    private function addBleeding($pid, $date, $pads=0, $tams=0, $spotting=0) {
        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            [
                'menstrual_blood_loss' => $spotting ? 0 : 1,
                'spotting' => $spotting ? true : false,
                'bl_pad_medium' => $pads,
                'bl_tampon_medium' => $tams,
                'is_blood_loss_answered' => true,
            ]
        );
    }

    private function addPain($pid, $date, $score) {
        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            [
                'pain_slider_value' => $score,
                'is_pain_answered' => true,
            ]
        );
    }

    private function addCustomPbac($pid, $date, $data) {
        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            $data
        );
    }
}
