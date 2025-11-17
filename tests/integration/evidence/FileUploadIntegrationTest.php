<?php
// tests/Integration/Evidence/FileUploadIntegrationTest.php

namespace Tests\Integration\Evidence;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Evidence;
use App\Models\File;
use App\Models\User;
use Tests\Integration\Evidence\Traits\EvidenceTestDataTrait;

class FileUploadIntegrationTest extends TestCase
{
    use RefreshDatabase, EvidenceTestDataTrait;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->createEvidenceTestData();
    }

    /** @test */
    public function it_uploads_files_to_evidence_successfully()
    {
        $this->actingAsProfessor();

        // Crear evidence primero
        $evidence = Evidence::create([
            'evidence_id' => 300,
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);

        $file = UploadedFile::fake()->create('document.zip', 1000); // 1MB file

        $response = $this->postJson('/file', [
            'evidence_id' => $evidence->evidence_id,
            'files' => [$file],
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([[
                    'file_id',
                    'file_url',
                    'upload_date',
                    'evidence_id',
                    'file_name'
                ]]);

        // Verificar que el archivo se almacenó
        Storage::disk('public')->exists('uploads/300/' . $response->json()[0]['file_url']);

        // Verificar que se creó el registro en la base de datos
        $this->assertDatabaseHas('files', [
            'evidence_id' => 300,
            'file_name' => 'document.zip'
        ]);

        // Verificar que se actualizó la justificación
        $this->assertDatabaseHas('evidences', [
            'evidence_id' => 300,
        ]);
    }

    /** @test */
    public function it_fails_upload_with_file_wrong_extension()
    {
        $this->actingAsProfessor();

        $evidence = Evidence::create([
            'evidence_id' => 400,
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);

        $file = UploadedFile::fake()->create('wrong_file.pdf', 1000); // .pdf - extensión no permitida

        $response = $this->postJson('/file', [
            'evidence_id' => $evidence->evidence_id,
            'files' => [$file]
        ]);

        $response->assertStatus(422); // Validation error
    }

    /** @test */
    public function it_fails_upload_with_file_too_large()
    {
        $this->actingAsProfessor();

        $evidence = Evidence::create([
            'evidence_id' => 400,
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);

        $file = UploadedFile::fake()->create('large_file.zip', 51201); // 50.001 MB - más del límite

        $response = $this->postJson('/file', [
            'evidence_id' => $evidence->evidence_id,
            'files' => [$file]
        ]);

        $response->assertStatus(422); // Validation error
    }

    /** @test */
    public function it_deletes_file_successfully()
    {
        $this->actingAsProfessor();

        // Crear evidence y file
        $evidence = Evidence::create([
            'evidence_id' => 500,
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);

        $file = File::create([
            'file_id' => 999,
            'file_url' => 'uploads/500/test_file.zip',
            'evidence_id' => 500,
            'file_name' => 'test_file.pdf',
            'upload_date' => now()
        ]);

        // Crear el archivo fake en storage
        Storage::disk('public')->put('uploads/500/test_file.zip', 'fake content');

        $response = $this->deleteJson("/file/{$file->file_id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Archivo eliminado correctamente'
                ]);

        // Verificar que se eliminó de la base de datos
        $this->assertDatabaseMissing('files', ['file_id' => 999]);

        // Verificar que se eliminó del storage
        Storage::disk('public')->assertMissing('uploads/500/test_file.zip');
    }
}