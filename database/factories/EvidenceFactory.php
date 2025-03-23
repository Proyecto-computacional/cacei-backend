<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Standard;
use App\Models\User;
use App\Models\Group;
use App\Models\Accreditation_Process;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evidence>
 */
class EvidenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evidence_id' => $this->faker->unique()->numberBetween(1, 1000),
            'standard_id' => Standard::factory(), // Relación con Standard
            'user_rpe' => User::factory(), // Relación con User
            'group_id' => Group::factory(), // Relación con Group
            'process_id' => Accreditation_Process::factory(), // Relación con Process
            'due_date' => $this->faker->dateTimeBetween('now', '+3 month'),
        ];
    }
}
