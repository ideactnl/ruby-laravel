<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $registration_number
 * @property string $login_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'login_at',
    ];

    public $timestamps = false;
}
