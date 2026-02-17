<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantAdditionalDetail extends Model
{
    protected $fillable = [
        'participant_id',
        'study_number',
        'dob',
    ];
}
