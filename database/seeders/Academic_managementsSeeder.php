<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Academic_managementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/academic_managments.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){

            $registros[] = [
                "academic_management_id" =>  preg_replace('/[^0-9]/', '',$row[0]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[1]),
                "job_position" => DatabaseSeeder::normalizeString($row[2]),
                "institution" => DatabaseSeeder::normalizeString($row[3]),
                "start_date" => DatabaseSeeder::normalizeNumeric($row[4]),
                "end_date" => DatabaseSeeder::normalizeNumeric($row[5]),
            ];
        }

        fclose($cvs);
        
        DB::table('academic_managements')->insert($registros);
    }
}
