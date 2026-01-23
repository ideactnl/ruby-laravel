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
        [$withCycle, $noCycle, $highBloodLoss] = $this->createParticipants();
        $today = Carbon::now()->startOfDay();

        $this->seedParticipantLikeParticipant1WithCycle($withCycle->id, $today);
        $this->seedParticipantLikeParticipant2NoCycle($noCycle->id, $today);
        $this->seedParticipantLikeParticipant2WithCycleAndHighBloodScore($highBloodLoss->id, $today);

        $this->command->info('Menstruation Wrapped test data generated successfully.');
        $this->command->info('Test Users: wrapped_participant1, wrapped_participant2, wrapped_participant3');
        $this->command->info('Password: "password", PIN: "123456"');
    }

    private function createParticipants(): array
    {
        $participants = [];

        $participants[] = Participant::firstOrCreate(
            ['registration_number' => 'wrapped_participant1'],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => true,
            ]
        );

        $participants[] = Participant::firstOrCreate(
            ['registration_number' => 'wrapped_participant2'],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => false,
            ]
        );

        $participants[] = Participant::firstOrCreate(
            ['registration_number' => 'wrapped_participant3'],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => false,
            ]
        );

        foreach ($participants as $p) {
            Pbac::where('participant_id', $p->id)->delete();
        }

        return $participants;
    }

    private function seedParticipantLikeParticipant1WithCycle(int $participantId, Carbon $today): void
    {
        $previousStart = $today->copy()->subDays(28);

        // Two start markers are required for wrapped calculation
        $this->addPeriodStart($participantId, $previousStart);
        $this->addPeriodStart($participantId, $today);

        // Menstruation phase (4 days) + some mid-cycle symptom days
        $this->addPatternDay($participantId, $previousStart, pattern: 'moderate', firstDay: true);
        $this->addPatternDay($participantId, $today, pattern: 'moderate', firstDay: true);
        $this->addPatternDay($participantId, $previousStart->copy()->addDay(), pattern: 'heavy');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(2), pattern: 'light');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(3), pattern: 'very_light');

        $this->addPatternDay($participantId, $previousStart->copy()->addDays(10), pattern: 'spotting');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(14), pattern: 'no_symptoms');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(20), pattern: 'light_pain');
    }

    private function seedParticipantLikeParticipant2NoCycle(int $participantId, Carbon $today): void
    {
        // Only one (or zero) start marker -> wrapped should return insufficient_data
        $this->addPeriodStart($participantId, $today->copy()->subDays(10));

        // Create PBAC entries similar shape to participant1 but without a complete cycle marker pair
        $this->addPatternDay($participantId, $today->copy()->subDays(10), pattern: 'moderate', firstDay: true);
        $this->addPatternDay($participantId, $today->copy()->subDays(9), pattern: 'light');
        $this->addPatternDay($participantId, $today->copy()->subDays(8), pattern: 'very_light');
        $this->addPatternDay($participantId, $today->copy()->subDays(3), pattern: 'no_symptoms');
    }

    private function seedParticipantLikeParticipant2WithCycleAndHighBloodScore(int $participantId, Carbon $today): void
    {
        $previousStart = $today->copy()->subDays(28);

        // Two start markers are required for wrapped calculation
        $this->addPeriodStart($participantId, $previousStart);
        $this->addPeriodStart($participantId, $today);

        // Menstruation phase (4 days) + some mid-cycle symptom days
        $this->addPatternDay($participantId, $previousStart, pattern: 'moderate', firstDay: true);
        $this->addPatternDay($participantId, $today, pattern: 'moderate', firstDay: true);
        $this->addPatternDay($participantId, $previousStart->copy()->addDay(), pattern: 'heavy');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(2), pattern: 'light');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(3), pattern: 'extreme');

        $this->addPatternDay($participantId, $previousStart->copy()->addDays(10), pattern: 'spotting');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(14), pattern: 'extreme');
        $this->addPatternDay($participantId, $previousStart->copy()->addDays(20), pattern: 'light_pain');
    }

    // Helpers
    private function addPeriodStart($pid, $date) {
        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            [
                'is_bl_first_day_period' => true,
                'menstrual_blood_loss' => 1,
                'is_blood_loss_answered' => true,
                'bl_pad_small' => 1,
            ]
        );
    }

    private function addPatternDay(int $pid, Carbon $date, string $pattern, bool $firstDay = false): void
    {
        $base = [
            'reported_date' => $date->format('Y-m-d'),
            'is_blood_loss_answered' => true,
            'is_pain_answered' => true,
            'is_impact_answered' => true,
            'is_bl_first_day_period' => $firstDay,
        ];

        $data = match ($pattern) {
            'extreme' => [
                'menstrual_blood_loss' => 1,
                'no_blood_loss' => false,
                'spotting' => false,
                'is_bl_heavy' => true,
                'is_bl_pads' => true,
                'bl_pad_medium' => 5,
                'bl_pad_large' => 8,
                'bl_tampon_large' => 9,
                'bl_tampon_medium' => 8,
                'pain_slider_value' => 6,
                'is_impact_missed_work' => true,
                'impact_slider_grade_your_day' => 2,
                'impact_slider_complaints' => 6,
            ],
            'heavy' => [
                'menstrual_blood_loss' => 1,
                'no_blood_loss' => false,
                'spotting' => false,
                'is_bl_heavy' => true,
                'is_bl_pads' => true,
                'bl_pad_medium' => 2,
                'bl_pad_large' => 1,
                'pain_slider_value' => 6,
                'is_impact_missed_work' => true,
                'impact_slider_grade_your_day' => 2,
                'impact_slider_complaints' => 6,
            ],
            'moderate' => [
                'menstrual_blood_loss' => 1,
                'no_blood_loss' => false,
                'spotting' => false,
                'is_bl_moderate' => true,
                'is_bl_pads' => true,
                'bl_pad_medium' => 2,
                'pain_slider_value' => 5,
                'is_impact_had_to_stay_longer_in_bed' => true,
                'impact_slider_grade_your_day' => 3,
                'impact_slider_complaints' => 5,
            ],
            'light' => [
                'menstrual_blood_loss' => 1,
                'no_blood_loss' => false,
                'spotting' => false,
                'is_bl_light' => true,
                'is_bl_pads' => true,
                'bl_pad_small' => 1,
                'bl_pad_medium' => 1,
                'pain_slider_value' => 3,
                'impact_slider_grade_your_day' => 7,
                'impact_slider_complaints' => 2,
            ],
            'very_light' => [
                'menstrual_blood_loss' => 1,
                'no_blood_loss' => false,
                'spotting' => false,
                'is_bl_very_light' => true,
                'is_bl_pads' => true,
                'bl_pad_small' => 1,
                'pain_slider_value' => 2,
                'impact_slider_grade_your_day' => 8,
                'impact_slider_complaints' => 1,
            ],
            'spotting' => [
                'menstrual_blood_loss' => 0,
                'no_blood_loss' => false,
                'spotting' => true,
                'pain_slider_value' => 1,
                'impact_slider_grade_your_day' => 8,
                'impact_slider_complaints' => 1,
            ],
            'light_pain' => [
                'menstrual_blood_loss' => 0,
                'no_blood_loss' => true,
                'spotting' => false,
                'pain_slider_value' => 4,
                'impact_slider_grade_your_day' => 7,
                'impact_slider_complaints' => 3,
            ],
            'no_symptoms' => [
                'menstrual_blood_loss' => 0,
                'no_blood_loss' => true,
                'spotting' => false,
                'no_pain' => true,
                'pain_slider_value' => 0,
                'impact_slider_grade_your_day' => 9,
                'impact_slider_complaints' => 0,
            ],
            default => [],
        };

        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $base['reported_date']],
            array_merge($base, $data)
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

    private function addImpact(int $pid, Carbon $date, array $data): void
    {
        $base = [
            'is_impact_answered' => true,
            'is_impact_used_medication' => false,
        ];

        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            array_merge($base, $data)
        );
    }

    private function addCustomPbac($pid, $date, $data) {
        Pbac::updateOrCreate(
            ['participant_id' => $pid, 'reported_date' => $date->format('Y-m-d')],
            $data
        );
    }
}
