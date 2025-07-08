<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'registration_number' => $this->faker->unique()->userName,
            'pin' => bcrypt('123456'),
            'opt_in_for_research' => true,
        ];
    }
}
