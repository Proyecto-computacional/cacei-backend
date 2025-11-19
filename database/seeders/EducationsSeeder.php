<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Education;

class EducationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/educations.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){

            $registros[] = [
                "education_id" =>  preg_replace('/[^0-9]/', '',$row[0]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[1]),
                "institution" => DatabaseSeeder::normalizeString($row[2]),
                "degree_obtained" => DatabaseSeeder::normalizeString($row[3]),
                "obtained_year" => DatabaseSeeder::normalizeNumeric($row[4]),
                "professional_license" => DatabaseSeeder::normalizeString($row[5]),
                "degree_name" => DatabaseSeeder::normalizeString($row[6])  
            ];
        }

        fclose($cvs);
        
        DB::table('educations')->insert($registros);
    }
}
