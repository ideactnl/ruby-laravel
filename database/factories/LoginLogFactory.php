<?php

namespace Database\Factories;

use App\Models\LoginLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoginLogFactory extends Factory
{
    protected $model = LoginLog::class;

    public function definition()
    {
        return [
            'registration_number' => $this->faker->unique()->userName,
            'login_at' => now(),
        ];
    }
}
