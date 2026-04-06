<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'session_id',
        'started_at',
        'last_seen_at',
        'duration_seconds',
        'section_breakdown',
        'interactions_breakdown',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'section_breakdown' => 'array',
        'interactions_breakdown' => 'array',
    ];

    /**
     * Get the participant that owns the session.
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
