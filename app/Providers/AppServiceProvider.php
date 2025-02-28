<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            DB::connection()->getPdo();
            $dbName = DB::connection()->getDatabaseName();
            Log::info("¡¡Conectado a la base de datos: {$dbName}!!");
            echo "¡¡Conectado a la base de datos: {$dbName}!!\n";
        } catch (\Exception $e) {
            Log::error("Error al conectar con la base de datos: " . $e->getMessage());
            echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
        }
    }
}
