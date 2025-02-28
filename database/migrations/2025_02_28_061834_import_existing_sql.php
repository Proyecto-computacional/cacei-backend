<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        DB::unprepared(file_get_contents(database_path('sql/Cacei.sql')));
    }

    public function down(): void {

    }
};
