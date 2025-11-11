<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class frame2025Seeder extends Seeder
{
    public function run(): void
    {
        $files = [
            'frame2025.sql',
            'categories2025.sql',
            'sections2025.sql',
            'standards2025.sql',
        ];

        foreach ($files as $file) {
            $path = database_path("seeders/sql/{$file}");
            if (file_exists($path)) {
                $sql = file_get_contents($path);
                DB::unprepared($sql);
                $this->command->info("Archivo ejecutado: {$file}");
            } else {
                $this->command->warn("Archivo no encontrado: {$file}");
            }
        }

        $this->command->info('Todos los archivos SQL del marco de referencia fueron importados.');
    }
}
