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
}
