<?php

namespace App\Services;

use App\Models\LoginLog;

class LoginLogService
{
    /**
     * Create a login log entry.
     *
     * @param string $registrationNumber
     * @return void
     */
    public function log(string $registrationNumber): void
    {
        LoginLog::create([
            'registration_number' => $registrationNumber,
            'login_at' => now(),
        ]);
    }
}
