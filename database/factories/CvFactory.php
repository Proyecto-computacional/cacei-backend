<?php
// database/factories/CvFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CvFactory extends Factory
{
    protected $model = \App\Models\Cv::class;

    public function definition()
    {
        return [
            'cv_id' => $this->faker->unique()->numberBetween(1, 10000),
            'professor_number' => $this->faker->unique()->numberBetween(100000, 999999),
            'professor_name' => $this->faker->name,
            'actual_position' => $this->faker->randomElement([
                'ADMINISTRADOR',
                'DIRECTIVO',
                'COORDINADOR DE CARRERA',
                'JEFE DE AREA',
                'PROFESOR RESPONSABLE',
                'PROFESOR',
                'TUTOR ACADEMICO',
                'DEPARTAMENTO UNIVERSITARIO',
                'PERSONAL DE APOYO']),
            'update_date' => $this->faker->date(),
            'age' => $this->faker->numberBetween(30, 70),
            'birth_date' => $this->faker->date(),
            'duration' => $this->faker->numberBetween(1, 30)
        ];
    }
}