<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Disciplinary_UpdatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/disciplinary_updates.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){

            $registros[] = [
                "disciplinary_update_id" =>  preg_replace('/[^0-9]/', '',$row[0]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[1]),
                "title_certification" => DatabaseSeeder::normalizeString($row[2]),
                "year_certification" => DatabaseSeeder::normalizeNumeric($row[3]),
                //"institution_country" => DatabaseSeeder::normalizeNumeric($row[4]),
                "hours" => DatabaseSeeder::normalizeNumeric($row[5]),
            ];
        }

        fclose($cvs);
        
        DB::table('disciplinary_updates')->insert($registros);
    }
}
