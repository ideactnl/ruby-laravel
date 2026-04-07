<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\Pbac;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ParticipantPbacSeeder extends Seeder
{
    private array $timeRanges = [
        'current_month' => ['months' => 0, 'records_per_month' => 10],
        'previous_month' => ['months' => 1, 'records_per_month' => 10],
        'previous_quarter' => ['months' => 3, 'records_per_month' => 15],
        'six_months_ago' => ['months' => 6, 'records_per_month' => 8],
        'previous_year' => ['months' => 12, 'records_per_month' => 12],
        'two_years_ago' => ['months' => 24, 'records_per_month' => 6],
    ];

    private array $patterns = ['severe', 'moderate', 'light', 'none'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pbac::truncate();

        $participants = $this->createParticipants();

        foreach ($participants as $participant) {
            $this->generateComprehensiveData($participant->id);
        }
    }

    /**
     * Create participant records
     */
    private function createParticipants(): array
    {
        $participants = [];

        $participants[] = Participant::firstOrCreate(
            ['registration_number' => 'participant1'],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => true,
            ]
        );

        $participants[] = Participant::firstOrCreate(
            ['registration_number' => 'participant2'],
            [
                'password' => bcrypt('password'),
                'pin' => bcrypt('123456'),
                'enable_data_sharing' => true,
                'opt_in_for_research' => false,
            ]
        );

        return $participants;
    }

    /**
     * Generate comprehensive PBAC data across multiple time ranges using factory
     */
    private function generateComprehensiveData(int $participantId): void
    {
        foreach ($this->timeRanges as $rangeName => $config) {
            $baseDate = Carbon::now()->subMonths($config['months'])->startOfMonth();
            $this->generateMonthlyData($participantId, $baseDate, $config['records_per_month']);
        }
    }

    /**
     * Generate varied PBAC records for a specific month using factory
     */
    private function generateMonthlyData(int $participantId, Carbon $baseDate, int $recordCount): void
    {
        $daysInMonth = $baseDate->daysInMonth;
        $dayInterval = max(1, floor($daysInMonth / $recordCount));

        for ($i = 0; $i < $recordCount; $i++) {
            $dayOffset = ($i * $dayInterval) + rand(1, min(3, $dayInterval));
            $recordDate = $baseDate->copy()->addDays(min($dayOffset, $daysInMonth - 1));

            $patternIndex = $i % count($this->patterns);
            $pattern = $this->patterns[$patternIndex];

            if (rand(1, 100) <= 30) {
                $pattern = $this->patterns[array_rand($this->patterns)];
            }

            $this->createPbacRecord($participantId, $recordDate, $pattern);
        }
    }

    /**
     * Create a single PBAC record using factory with specified pattern
     */
    private function createPbacRecord(int $participantId, Carbon $date, string $pattern): void
    {
        // Check if record already exists for this participant and date
        $existingRecord = Pbac::where('participant_id', $participantId)
            ->where('reported_date', $date->format('Y-m-d'))
            ->first();

        if (! $existingRecord) {
            Pbac::factory()->{$pattern}()->create([
                'participant_id' => $participantId,
                'reported_date' => $date->format('Y-m-d'),
            ]);
        }
    }
}
