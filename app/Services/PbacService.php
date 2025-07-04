<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Pbac;
use App\Models\User;
use Exception;

class PbacService
{
    /**
     * Retrieve PBAC records for a user, with optional filters.
     *
     * @param string $registration_number  User's registration number
     * @param int|null $id                 PBAC record ID (optional)
     * @param int|null $day                Day to filter by (optional)
     * @param int|null $month              Month to filter by (optional)
     * @param int|null $year               Year to filter by (optional)
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Pbac|null
     */
    public function getUserPbacs($registration_number, $id = null, $day = null, $month = null, $year = null)
    {
        $user = User::where('registration_number', $registration_number)->first();
        if (!$user)
            return $id ? null : collect();
        $query = Pbac::where('user_id', $user->id);
        if ($id)
            $query->where('id', $id);
        if ($year)
            $query->whereYear('reported_date', $year);
        if ($month)
            $query->whereMonth('reported_date', $month);
        if ($day)
            $query->whereDay('reported_date', $day);
        return $id ? $query->first() : $query->orderBy('reported_date')->get();
    }

    /**
     * Create or update a PBAC record by registration number and reported date.
     *
     * @param array $input  Associative array of input data (must include 'registration_number' and 'reported_date')
     * @return array        [Pbac $pbac, bool $created]
     * @throws Exception    If user or reported_date is missing
     */
    public function upsertByRegistrationAndDate(array $input)
    {
        $user = User::where('registration_number', $input['registration_number'] ?? null)->first();
        if (!$user) {
            throw new Exception('User not found');
        }
        $reportedDate = $input['reported_date'] ?? $input['ReportedDate'] ?? null;
        if (!$reportedDate) {
            throw new Exception('Reported date is required');
        }
        $pbac = Pbac::firstOrNew([
            'user_id' => $user->id,
            'reported_date' => $reportedDate,
        ]);
        $created = !$pbac->exists;
        $pbac->fill(Pbac::mapLegacyInputFields($input));
        $pbac->save();
        return [$pbac, $created];
    }

    /**
     * Check if a user exists by registration number.
     *
     * @param string|null $registration_number  User's registration number
     * @return User|null                        The User model or null if not found
     */
    public function checkUser($registration_number = null)
    {
        if (!$registration_number) {
            return null;
        }
        return User::where('registration_number', $registration_number)->first();
    }
}
