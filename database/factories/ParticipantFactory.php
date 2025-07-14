<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Participant;

class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition(): array
    {
        return [
            'registration_number' => $this->faker->unique()->numerify('REG#####'),
            'pin' => bcrypt('1234'),
            'password' => bcrypt('password'),
            'enable_data_sharing' => $this->faker->boolean,
            'opt_in_for_research' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
