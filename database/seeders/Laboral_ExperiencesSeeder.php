<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Laboral_ExperiencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/laboral_experiences.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){

            $registros[] = [
                "laboral_experience_id" =>  preg_replace('/[^0-9]/', '',$row[0]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[1]),
                "company_name" => DatabaseSeeder::normalizeString($row[2]),
                "position" => DatabaseSeeder::normalizeString($row[3]),
                "start_date" => DatabaseSeeder::normalizeString($row[4]),
                "end_date" => DatabaseSeeder::normalizeString($row[5]),
            ];
        }

        fclose($cvs);
        
        DB::table('laboral_experiences')->insert($registros);
    }
}
