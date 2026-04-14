<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\ParticipantSession;
use Illuminate\Support\Facades\Session;

class ParticipantSessionService
{
    /**
     * Start a new session record or find an existing one for the current request.
     */
    public function startSession(Participant $participant): void
    {
        $sessionId = Session::getId();

        ParticipantSession::firstOrCreate(
            [
                'participant_id' => $participant->id,
                'session_id' => $sessionId,
            ],
            [
                'started_at' => now(),
                'last_seen_at' => now(),
                'duration_seconds' => 0,
                'section_breakdown' => [],
                'interactions_breakdown' => [],
            ]
        );
    }

    /**
     * Update the heartbeat and aggregate duration metrics.
     */
    public function heartbeat($participant, $section = null, $isVisit = false)
    {
        $session = ParticipantSession::where('participant_id', $participant->id)
            ->where('session_id', Session::getId())
            ->first();

        if ($session) {
            $now = now();
            // Update duration and section breakdown
            if ($section) {
                $lastSeen = $session->last_seen_at ?? $session->created_at;
                $diff = max(0, $now->getTimestamp() - $lastSeen->getTimestamp());

                // Throttle: only add if diff is reasonable (handle tab switching)
                if ($diff > 0 && $diff < 120) {
                    $session->duration_seconds += (int) $diff;

                    $breakdown = $session->section_breakdown ?? [];
                    $breakdown[$section] = ($breakdown[$section] ?? 0) + $diff;
                    $session->section_breakdown = $breakdown;
                }

                // Increment Page Visit count if this is an initial load
                if ($isVisit) {
                    $visits = $session->interactions_breakdown ?? [];
                    $visits[$section] = ($visits[$section] ?? 0) + 1;
                    $session->interactions_breakdown = $visits;
                }
            }

            $session->last_seen_at = $now;
            $session->save();
        } else {
            // Re-start if session missing
            $this->startSession($participant);
        }
    }
}
