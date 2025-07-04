<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'registration_number',
        'login_at',
    ];

    public $timestamps = false;

}
