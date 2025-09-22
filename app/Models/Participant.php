<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $registration_number
 * @property string $password
 * @property string $pin
 * @property bool|null $enable_data_sharing
 * @property bool|null $opt_in_for_research
 * @property bool|null $allow_medical_specialist_login
 * @property string|null $medical_specialist_temporary_pin
 * @property \Carbon\CarbonInterface|null $medical_specialist_temporary_pin_expires_at
 * @property \Carbon\CarbonInterface|null $created_at
 * @property \Carbon\CarbonInterface|null $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Participant extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ParticipantFactory> */
    use HasApiTokens, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'registration_number',
        'password',
        'pin',
        'enable_data_sharing',
        'opt_in_for_research',
        'allow_medical_specialist_login',
        'medical_specialist_temporary_pin',
        'medical_specialist_temporary_pin_expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin',
        'medical_specialist_temporary_pin',
    ];

    protected $casts = [
        'medical_specialist_temporary_pin_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
