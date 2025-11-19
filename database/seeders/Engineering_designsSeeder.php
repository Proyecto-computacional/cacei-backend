<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Engineering_designsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/engineering_desings.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){

            $registros[] = [
                "engineering_design_id" =>  preg_replace('/[^0-9]/', '',$row[0]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[1]),
                "institution" => DatabaseSeeder::normalizeString($row[2]),
                "period" => DatabaseSeeder::normalizeNumeric($row[3]),
                "level_experience" => DatabaseSeeder::normalizeString($row[4]),
            ];
        }

        fclose($cvs);
        
        DB::table('engineering_designs')->insert($registros);
    }
}
