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
    protected $model = Evidence::class;

    public function definition()
    {
        return [
            'standard_id' => \App\Models\Standard::factory(),
            'user_rpe' => \App\Models\User::factory(),
            'group_id' => $this->faker->numberBetween(1, 10),
            'process_id' => $this->faker->numberBetween(1, 10),
            'due_date' => $this->faker->date()
        ];
    }
}
