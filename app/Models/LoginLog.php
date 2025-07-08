<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_number',
        'login_at',
    ];

    public $timestamps = false;

}
