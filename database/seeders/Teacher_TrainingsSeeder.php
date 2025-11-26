<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TeacherTraining;

class Teacher_TrainingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/teacher_trainings.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){

            $registros[] = [
                "teacher_training_id" =>  preg_replace('/[^0-9]/', '',$row[0]),
                "title_certification" => DatabaseSeeder::normalizeString($row[1]),
                "obtained_year" => DatabaseSeeder::normalizeNumeric($row[2]),
                "institution_country" => DatabaseSeeder::normalizeString($row[3]),
                "hours" => DatabaseSeeder::normalizeNumeric($row[4]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[5])
            ];
        }

        fclose($cvs);
        
        DB::table('teacher_trainings')->insert($registros);
    }
}
