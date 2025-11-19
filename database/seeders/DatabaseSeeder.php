<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**Los datos de la tabla del cv seran proporcionadoa mediante un servicio web */
        /**por lo tanto sera la unica que no se ejecute */
        /**Los tamaños de algunos campos deben ser cambiados, puesto que las entradas
         * son mas grandes que los valores de longitud de la base de datos actual
         */
        $this->call([
            /**Esta funcion ejecuta todos los seeders, pero para debuggear errores 
             * de tamaño de variable recomiendo hacerlo uno por uno
             * comentando los que no se quieran ejecutar
             * o en su defecto ejecutar uno por uno en la terminal usando
             * php artisan db:seed --class=NombreDelSeeder
             * 
             */
            AwardsSeeder::class,
            Academic_managementsSeeder::class,
            Academic_productsSeeder::class,
            AwardsSeeder::class,
            Contruibutions_to_peSeeder::class,
            Disciplinary_UpdatesSeeder::class,
            EducationsSeeder::class,
            Engineering_designsSeeder::class,
            Laboral_ExperiencesSeeder::class,
            ParticipationsSeeder::class,
            Professional_achievementsSeeder::class,
            Teacher_TrainingsSeeder::class
        ]);
    }

    public static function normalizeString($value)
    {
        // Si viene como texto literal "NULL", lo convertimos a string vacío
        if (strtoupper(trim($value)) === "NULL") {
            return "";
        }

        return $value;
    }

    public static function normalizeNumeric($value)
    {
        return strtoupper(trim($value)) === "NULL" || trim($value) === "" 
            ? null 
            : preg_replace('/[^0-9]/', '', $value);
    }


}
