<?php

namespace App\Services;

use App\Models\LoginLog;

class LoginLogService
{
    /**
     * Create a login log entry.
     */
    public function log(string $registrationNumber): void
    {
        LoginLog::create([
            'registration_number' => $registrationNumber,
            'login_at' => now(),
        ]);
    }
}
