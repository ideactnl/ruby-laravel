<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class ParticipantActivityLogService
{
    protected string $logName = 'participant-visits';

    /**
     * Log a dashboard visit for a participant with throttling.
     */
    public function logDashboardVisit(Participant $participant): void
    {
        $cacheKey = "participant_dashboard_visit_{$participant->id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        activity($this->logName)
            ->performedOn($participant)
            ->causedBy($participant)
            ->withProperties([
                'event' => 'dashboard_visit',
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ])
            ->log('Participant visited the dashboard');

        // Throttle for 5 minutes to prevent duplicate logs from refreshes
        Cache::put($cacheKey, true, now()->addMinutes(5));
    }
}
