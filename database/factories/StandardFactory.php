<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Standard>
 */
class StandardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section_id' => \App\Models\Section::factory(),
            'standard_name' => $this->faker->word(),
            'standard_description' => $this->faker->sentence(),
            'is_transversal' => $this->faker->boolean(),
            'help' => $this->faker->sentence()
        ];
    }
}
