<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/awards.CSV');
        $cvs = fopen($file,'r');

        $registros = [];

        while(($row = fgetcsv($cvs)) !== false){
            $registros[] = [
                "award_id" => preg_replace('/[^0-9]/', '', $row[0]),
                "cv_id" => preg_replace('/[^0-9]/', '',$row[1]),
                "description" => $row[2],
            ];
        }

        fclose($cvs);
        
        DB::table('awards')->insert($registros);
    }
}
