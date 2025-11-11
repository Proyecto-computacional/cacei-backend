<?php
// tests/Integration/Evidence/EvidenceCrudIntegrationTest.php

namespace Tests\Integration\Evidence;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Evidence;
use Tests\Integration\Evidence\Traits\EvidenceTestDataTrait;

class EvidenceCrudIntegrationTest extends TestCase
{
    use RefreshDatabase, EvidenceTestDataTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createEvidenceTestData();
    }

    /** @test */
    public function it_creates_evidence_successfully()
    {
        // AUTENTICAR como admin antes de hacer la petición
        $this->actingAsAdmin();

        $payload = $this->getCreateEvidencePayload();

        $response = $this->postJson('/evidence', $payload);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Asignado exitosamente'
                ])
                ->assertJsonStructure([
                    'evidence' => [
                        'evidence_id',
                        'standard_id',
                        'user_rpe',
                        'process_id',
                        'due_date'
                    ]
                ]);

        // Verificar que se creó en la base de datos
        $this->assertDatabaseHas('evidences', [
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1
        ]);
    }

    /** @test */
    public function it_shows_evidence_with_relationships()
    {
        $this->actingAsProfessor();

        // Primero crear una evidence
        $evidence = Evidence::create([
            'evidence_id' => 999,
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);

        $response = $this->getJson("/evidences/{$evidence->evidence_id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'evidence' => [
                        'evidence_id',
                        'standard_id',
                        'user_rpe',
                        'process_id',
                        'due_date',
                        'process',
                        'standard',
                        'files',
                        'status'
                    ],
                    'first_revisor'
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_evidence()
    {
        $this->actingAsProfessor();

        $response = $this->getJson('/evidences/99999');

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Evidencia no encontrada'
                ]);
    }

    /** @test */
    public function it_updates_evidence_justification()
    {
        $this->actingAsProfessor();

        $evidence = Evidence::create([
            'evidence_id' => 888,
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);

        $updateData = [
            'justification' => 'Justificación actualizada para pruebas'
        ];

        $response = $this->putJson("/evidences/{$evidence->evidence_id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Evidencia actualizada correctamente'
                ]);

        $this->assertDatabaseHas('evidences', [
            'evidence_id' => 888,
            'justification' => '<p>Justificación actualizada para pruebas</p>'
        ]);
    }

    /** @test */
    public function it_gets_evidences_by_standard()
    {
        $this->actingAsProfessor();

        // Crear evidencias para diferentes estándares
        Evidence::create([
            'evidence_id' => 200, 
            'standard_id' => 1, 
            'user_rpe' => 'PROF001', 
            'process_id' => 1, 
            'due_date' => '2024-12-31'
        ]);
        
        Evidence::create([
            'evidence_id' => 201, 
            'standard_id' => 2, 
            'user_rpe' => 'PROF001', 
            'process_id' => 1, 
            'due_date' => '2024-12-31'
        ]);

        $response = $this->getJson('/evidences/by-standard/1');

        $response->assertStatus(200);

        $evidences = $response->json();
        $this->assertCount(1, $evidences);
        $this->assertEquals(1, $evidences[0]['standard_id']);
    }

    /** @test */
    public function it_fails_without_authentication()
    {
        // NO autenticar - debería fallar
        $response = $this->getJson('/evidences/999');
        $response->assertStatus(401);
    }
}