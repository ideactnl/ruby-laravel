<?php

namespace Database\Factories;

use App\Models\Pbac;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbacFactory extends Factory
{
    protected $model = Pbac::class;

    public function definition()
    {
        return [
            'participant_id' => function () {
                return \App\Models\Participant::factory()->create()->id;
            },
            'reported_date' => $this->faker->date(),
            'created_date' => $this->faker->dateTime(),
            'q3a' => $this->faker->numberBetween(0, 10),
            'q3b' => $this->faker->numberBetween(0, 10),
            'q3c' => $this->faker->numberBetween(0, 10),
            'q3d' => $this->faker->numberBetween(0, 10),
        ];
    }
}
