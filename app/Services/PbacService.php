<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\Pbac;
use Exception;

class PbacService
{
    /**
     * Retrieve PBAC records for a participant, with optional filters.
     *
     * @param  string  $registration_number  Participant's registration number
     * @param  int|null  $id  PBAC record ID (optional)
     * @param  int|null  $day  Day to filter by (optional)
     * @param  int|null  $month  Month to filter by (optional)
     * @param  int|null  $year  Year to filter by (optional)
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Pbac|null
     */
    public function getParticipantPbacs($registration_number, $id = null, $day = null, $month = null, $year = null)
    {
        $participant = Participant::where('registration_number', $registration_number)->first();
        if (! $participant) {
            return $id ? null : (new Pbac)->newCollection();
        }
        $query = Pbac::where('participant_id', $participant->id);
        if ($id) {
            $query->where('id', $id);
        }
        if ($year) {
            $query->whereYear('reported_date', $year);
        }
        if ($month) {
            $query->whereMonth('reported_date', $month);
        }
        if ($day) {
            $query->whereDay('reported_date', $day);
        }

        return $id ? $query->first() : $query->orderBy('reported_date')->get();
    }

    /**
     * Create or update a PBAC record by registration number and reported date.
     *
     * @param  array  $input  Associative array of input data (must include 'registration_number' and 'reported_date')
     * @return array [Pbac $pbac, bool $created]
     *
     * @throws Exception If participant or reported_date is missing
     */
    public function upsertByRegistrationAndDate(array $input)
    {
        $participant = Participant::where('registration_number', $input['registration_number'] ?? null)->first();
        if (! $participant) {
            throw new Exception('Participant not found');
        }
        $reportedDate = $input['reported_date'] ?? $input['reportedDate'] ?? null;
        if (! $reportedDate) {
            throw new Exception('Reported date is required');
        }
        $pbac = Pbac::firstOrNew([
            'participant_id' => $participant->id,
            'reported_date' => $reportedDate,
        ]);
        $created = ! $pbac->exists;
        $data = Pbac::camelToSnake($input);
        $data['reported_date'] = $reportedDate;
        unset($data['registration_number'], $data['reporteddate'], $data['ReportedDate']);
        $pbac->fill($data);
        $pbac->save();

        return [$pbac, $created];
    }

    /**
     * Check if a participant exists by registration number.
     *
     * @param  string|null  $registration_number  Participant's registration number
     * @return Participant|null The Participant model or null if not found
     */
    public function checkParticipant($registration_number = null)
    {
        if (! $registration_number) {
            return null;
        }

        return Participant::where('registration_number', $registration_number)->first();
    }

    /**
     * Get the 'Menstruation Wrapped' data for the last cycle.
     */
    public function getMenstruationWrappedData(int $participantId): array
    {
        $periodStartDates = Pbac::where('participant_id', $participantId)
            ->where('is_bl_first_day_period', true)
            ->orderByDesc('reported_date')
            ->take(2)
            ->pluck('reported_date');

        if ($periodStartDates->count() < 2) {
            return [
                'can_calculate' => false,
                'reason' => 'insufficient_data',
            ];
        }

        $mostRecentStart = $periodStartDates[0];
        $previousStart = $periodStartDates[1];

        $cycleEnd = $mostRecentStart->copy()->subDay();
        $cycleLength = $previousStart->diffInDays($mostRecentStart);

        if ($cycleLength > 60) {
            return [
                'can_calculate' => false,
                'reason' => 'cycle_too_long',
                'cycle_length' => $cycleLength,
            ];
        }

        $records = Pbac::where('participant_id', $participantId)
            ->whereBetween('reported_date', [$previousStart, $cycleEnd])
            ->get();

        $bloodLossDays = $records->where('menstrual_blood_loss', '>', 0)->pluck('reported_date')->unique()->count();
        $spottingDays = $records->where('spotting', true)->pluck('reported_date')->unique()->count();

        // 4. PBAC Score calculation
        $pbacScore = $records->sum(fn ($r) => ($r->bl_pad_small ?? 0) * 1 +
            ($r->bl_pad_medium ?? 0) * 5 +
            ($r->bl_pad_large ?? 0) * 20 +
            ($r->bl_tampon_small ?? 0) * 1 +
            ($r->bl_tampon_medium ?? 0) * 5 +
            ($r->bl_tampon_large ?? 0) * 20);

        $painDays = $records->where('pain_slider_value', '>', 2)->pluck('reported_date')->unique()->count();
        $extremePainDays = $records->where('pain_slider_value', '>', 5)->pluck('reported_date')->unique()->count();

        $impactDays = $records->filter(function ($r) {
            return $r->is_impact_missed_work ||
                $r->is_impact_missed_school ||
                $r->is_impact_could_not_sport ||
                $r->is_impact_missed_special_activities ||
                $r->is_impact_missed_leisure_activities ||
                $r->is_impact_had_to_sit_more ||
                $r->is_impact_could_not_move ||
                $r->is_impact_had_to_stay_longer_in_bed ||
                $r->is_impact_could_not_do_unpaid_work ||
                $r->is_impact_other;
        })->pluck('reported_date')->unique()->count();

        return [
            'can_calculate' => true,
            'start_date' => $previousStart->format('Y-m-d'),
            'end_date' => $cycleEnd->format('Y-m-d'),
            'cycle_length' => $cycleLength,
            'blood_loss_days' => $bloodLossDays,
            'spotting_days' => $spottingDays,
            'pbac_score' => $pbacScore,
            'show_pbac_high' => $pbacScore > 150,
            'pain_days' => $painDays,
            'extreme_pain_days' => $extremePainDays,
            'impact_days' => $impactDays,
        ];
    }
}
