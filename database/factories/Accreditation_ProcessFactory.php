<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accreditation_Process>
 */
class Accreditation_ProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'frame_id' =>  $this->faker->unique()->numberBetween(1, 1000),
            'process_id' =>  $this->faker->unique()->numberBetween(1, 1000),
            'career_id' =>  $this->faker->unique()->numberBetween(1, 1000),
        ];
    }
}
