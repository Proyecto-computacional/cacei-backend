<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('cvs')->insert([
            [
                'cv_id' => 1, 'professor_number' => 10285, 
                'update_date' => '2025-03-15', 'professor_name' => 'RAMOS BLANCO ALBERTO', 
                'age' => 40, 'birth_date' => '1985-03-15', 'actual_position' => 'Profesor', 
                'duration' => 15
            ],
            [
                'cv_id' => 2, 'professor_number' => 10314, 
                'update_date' => '2025-03-15', 'professor_name' => 'MARTINEZ PEREZ FRACISCO EDUARDO', 
                'age' => 40, 'birth_date' => '1985-03-15', 'actual_position' => 'Coordinador de carrera', 
                'duration' => 15
            ],
                        [
                'cv_id' => 3, 'professor_number' => 18220, 
                'update_date' => '2025-03-15', 'professor_name' => 'REYES CARDENAS OSCAR', 
                'age' => 40, 'birth_date' => '1985-03-15', 'actual_position' => 'Profesor', 
                'duration' => 15
            ],
                        [
                'cv_id' => 4, 'professor_number' => 10887, 
                'update_date' => '2025-03-15', 'professor_name' => 'VACA RIVERA SILVIA LUZ', 
                'age' => 40, 'birth_date' => '1985-03-15', 'actual_position' => 'Jefe de area', 
                'duration' => 15
            ],
                        [
                'cv_id' => 5, 'professor_number' => 3045, 
                'update_date' => '2025-03-15', 'professor_name' => 'DIAZ QUIÑONES LILIA DEL CARMEN', 
                'age' => 40, 'birth_date' => '1985-03-15', 'actual_position' => 'Profesor', 
                'duration' => 15
            ],
        ]);

        DB::table('users')->insert([
            [
                'user_rpe'=> '10285', 'user_mail' => 'beto@uaslp.mx', 'user_role' => 'PROFESOR', 
                'user_name' => 'RAMOS BLANCO ALBERTO', 'user_area' => 2, 'cv_id' => 1, 
                'situation' => 'Activo'
            ],
            [
                'user_rpe'=> '10314', 'user_mail' => 'eduardo.perez@uaslp.mx', 'user_role' => 'COORDINADOR DE CARRERA', 
                'user_name' => 'MARTINEZ PEREZ FRACISCO EDUARDO', 'user_area' => 2, 'cv_id' => 2, 
                'situation' => 'Activo'
            ],
            [
                'user_rpe'=> '18220', 'user_mail' => 'oscar.reyes@uaslp.mx', 'user_role' => 'PROFESOR', 
                'user_name' => 'REYES CARDENAS OSCAR', 'user_area' => 2, 'cv_id' => 3, 
                'situation' => 'Activo'
            ],
            [
                'user_rpe'=> '10887', 'user_mail' => 'silviav@uaslp.mx', 'user_role' => 'JEFE DE AREA', 
                'user_name' => 'VACA RIVERA SILVIA LUZ', 'user_area' => 2, 'cv_id' => 4, 
                'situation' => 'Activo'
            ],
            [
                'user_rpe'=> '3045', 'user_mail' => 'diaquili@uaslp.mx', 'user_role' => 'ADMINISTRADOR', 
                'user_name' => 'DIAZ QUIÑONES LILIA DEL CARMEN', 'user_area' => 3, 'cv_id' => 5, 
                'situation' => 'Activo'
            ],
        ]);
    }
}
