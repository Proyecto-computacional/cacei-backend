<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cv', function (Blueprint $table) {
            $table->id('cv_id');
            $table->integer('professor_number')->unique();
            $table->date('update_date')->nullable();
            $table->string('professor_name', 25);
            $table->integer('age');
            $table->date('birth_date')->nullable();
            $table->string('actual_position', 20);
            $table->integer('duration');
            $table->timestamps();
        });

        Schema::create('user_t', function (Blueprint $table) {
            $table->string('user_rpe', 20)->primary();
            $table->string('user_mail', 100)->unique();
            $table->string('user_role', 20);
            $table->unsignedBigInteger('cv_id')->nullable();
            $table->foreign('cv_id')->references('cv_id')->on('cv')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('frame_of_reference', function (Blueprint $table) {
            $table->id('frame_id');
            $table->string('frame_name', 20);
            $table->timestamps();
        });

        Schema::create('category', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name', 50);
            $table->unsignedBigInteger('frame_id');
            $table->foreign('frame_id')->references('frame_id')->on('frame_of_reference')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('section_t', function (Blueprint $table) {
            $table->id('section_id');
            $table->unsignedBigInteger('category_id');
            $table->string('section_name', 25);
            $table->string('section_description', 50);
            $table->foreign('category_id')->references('category_id')->on('category')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('standard', function (Blueprint $table) {
            $table->id('standard_id');
            $table->unsignedBigInteger('section_id');
            $table->string('standard_name', 25);
            $table->string('standard_description', 50);
            $table->boolean('is_transversal');
            $table->string('help', 255)->nullable();
            $table->foreign('section_id')->references('section_id')->on('section_t')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('evidence', function (Blueprint $table) {
            $table->id('evidence_id');
            $table->unsignedBigInteger('standard_id');
            $table->string('user_rpe', 20);
            $table->integer('group_id');
            $table->integer('process_id');
            $table->date('due_date');
            $table->foreign('standard_id')->references('standard_id')->on('standard')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('file_t', function (Blueprint $table) {
            $table->id('file_id');
            $table->string('file_url', 255);
            $table->date('upload_date');
            $table->unsignedBigInteger('evidence_id');
            $table->foreign('evidence_id')->references('evidence_id')->on('evidence')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('notification', function (Blueprint $table) {
            $table->id('notification_id');
            $table->string('title', 20);
            $table->unsignedBigInteger('evidence_id');
            $table->date('notification_date');
            $table->string('user_rpe', 20);
            $table->string('description', 20)->nullable();
            $table->boolean('seen');
            $table->foreign('evidence_id')->references('evidence_id')->on('evidence')->onDelete('cascade');
            $table->foreign('user_rpe')->references('user_rpe')->on('user_t')->onDelete('cascade');
            $table->timestamps();
        });

        // Agregar el resto de las tablas 
    }

    public function down(): void {
        Schema::dropIfExists('notification');
        Schema::dropIfExists('file_t');
        Schema::dropIfExists('evidence');
        Schema::dropIfExists('standard');
        Schema::dropIfExists('section_t');
        Schema::dropIfExists('category');
        Schema::dropIfExists('frame_of_reference');
        Schema::dropIfExists('user_t');
        Schema::dropIfExists('cv');
        // Agregar drop para el resto de las tablas
    }
};
