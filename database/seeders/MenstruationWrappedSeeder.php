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
    public function run(): void
    {
        // 1. Create test participant
        $participant = Participant::firstOrCreate(
            ['registration_number' => 'wrapped_user'],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => true,
            ]
        );

        $id = $participant->id;

        // Cleanup existing for this user
        Pbac::where('participant_id', $id)->delete();

        $today = Carbon::now()->startOfDay();
        $recentPeriodStart = $today->copy()->subDays(2);
        $previousPeriodStart = $today->copy()->subDays(30);

        // A. Most recent period start (marks the end of the previous cycle summary)
        Pbac::create([
            'participant_id' => $id,
            'reported_date' => $recentPeriodStart->format('Y-m-d'),
            'is_bl_first_day_period' => true,
        ]);

        // B. Previous period start (The start of the cycle being wrapped)
        // This record is AT the start date (30 days ago)
        Pbac::create([
            'participant_id' => $id,
            'reported_date' => $previousPeriodStart->format('Y-m-d'),
            'is_bl_first_day_period' => true,
            'is_blood_loss_answered' => true,
            'menstrual_blood_loss' => 1,
            'is_bl_heavy' => true,
            'bl_pad_large' => 5, // 5 * 20 = 100 points
            'pain_slider_value' => 4, // > 2 -> Pain day
            'is_pain_answered' => true,
        ]);

        // C. Heavy blood loss + Extreme Pain + Impact
        Pbac::create([
            'participant_id' => $id,
            'reported_date' => $previousPeriodStart->copy()->addDays(2)->format('Y-m-d'),
            'is_blood_loss_answered' => true,
            'menstrual_blood_loss' => 1,
            'bl_tampon_large' => 4, // 4 * 20 = 80 points. Total 180.
            'pain_slider_value' => 8, // > 5 -> Extreme pain day
            'is_pain_answered' => true,
            'is_impact_missed_work' => true, // Impact day
        ]);

        // D. Spotting Day
        Pbac::create([
            'participant_id' => $id,
            'reported_date' => $previousPeriodStart->copy()->addDays(5)->format('Y-m-d'),
            'is_blood_loss_answered' => true,
            'spotting' => true,
            'menstrual_blood_loss' => 0,
        ]);

        // E. Mid-cycle Pain + Impact
        Pbac::create([
            'participant_id' => $id,
            'reported_date' => $previousPeriodStart->copy()->addDays(15)->format('Y-m-d'),
            'pain_slider_value' => 6, // Extreme pain
            'is_pain_answered' => true,
            'is_impact_could_not_sport' => true, // Impact day
        ]);

        // F. Mild blood loss day
        Pbac::create([
            'participant_id' => $id,
            'reported_date' => $previousPeriodStart->copy()->addDays(25)->format('Y-m-d'),
            'is_blood_loss_answered' => true,
            'menstrual_blood_loss' => 1,
            'bl_pad_small' => 5, // 5 points. Total 185.
        ]);
        
        $this->command->info('Menstruation Wrapped test data generated for user: wrapped_user');
        $this->command->info('Metrics: 28 day cycle, 185 PBAC score, 3 blood loss days, 1 spotting day, 3 pain days, 2 extreme pain days, 2 impact days.');
    }
}
