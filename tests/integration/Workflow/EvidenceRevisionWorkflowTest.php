<?php
// tests/Integration/Workflow/EvidenceRevisionWorkflowTest.php

namespace Tests\Integration\Workflow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Evidence;
use App\Models\Status;
use App\Models\Notification;
use App\Models\User;
use Tests\Integration\Workflow\Traits\RevisionWorkflowTrait;

class EvidenceRevisionWorkflowTest extends TestCase
{
    use RefreshDatabase, RevisionWorkflowTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRevisionWorkflowData();
    }

    /** @test */
    public function it_completes_normal_revision_workflow_profesor_to_coordinador()
    {
        // Crear evidence
        $evidence = $this->createWorkflowEvidence(100);

        // COORDINADOR aprueba
        $this->actingAsUser('COORD001');

        $response = $this->postJson('/RevisionEvidencias/aprobar', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe, // PROF001 - USUARIO QUE RECIBE LA NOTIFICACIÓN
            'feedback' => 'Buen trabajo, aprobado por coordinador',
            'reviser_rpe' => 'COORD001'
        ]);

        // DEBUG: Verificar autenticación
        if ($response->status() !== 200) {
            dump('AUTH DEBUG:');
            dump('Authenticated User:', auth()->user() ? auth()->user()->user_rpe : 'NO AUTH');
            dump('Response Status:', $response->status());
            dump('Response Content:', $response->getContent());
        }

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Evidencia marcada como APROBADA'
                ]);

        // Notificación se envía al user_rpe del request (PROF001)
        $this->assertDatabaseHas('notifications', [
            'evidence_id' => 100,
            'user_rpe' => 'PROF001', // DESTINATARIO de la notificación
            'reviser_id' => 'COORD001', // QUIÉN generó la notificación
            'title' => 'Evidencia APROBADA'
        ]);

        // Se asigna siguiente revisor (JEFE001)
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 100,
            'user_rpe' => 'JEFE001',
            'status_description' => 'PENDIENTE'
        ]);
    }

    /** @test */
    public function it_completes_full_revision_workflow_to_administrador()
    {
        $evidence = $this->createWorkflowEvidence(200);

        // 1. COORDINADOR aprueba
        $this->actingAs(User::where('user_rpe', 'COORD001')->first());
        $this->postJson('/RevisionEvidencias/aprobar', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe, // PROF001 - dueño
            'feedback' => 'Aprobado por coordinador'
        ]);

        // 2. JEFE DE ÁREA aprueba (debería asignar al ADMINISTRADOR)
        $this->actingAs(User::where('user_rpe', 'JEFE001')->first());
        $response = $this->postJson('/RevisionEvidencias/aprobar', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe, // PROF001 - dueño
            'feedback' => 'Aprobado por jefe de área'
        ]);

        $response->assertStatus(200);

        // Verificar status de JEFE DE ÁREA (revisor)
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 200,
            'user_rpe' => 'JEFE001', // El revisor actual
            'status_description' => 'APROBADA'
        ]);

        // Verificar que se creó status PENDIENTE para ADMINISTRADOR (siguiente)
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 200,
            'user_rpe' => 'ADMIN001', // Siguiente en el flujo
            'status_description' => 'PENDIENTE'
        ]);
    }

    /** @test */
    public function it_handles_evidence_rejection_by_coordinador()
    {
        $evidence = $this->createWorkflowEvidence(300);

        // COORDINADOR rechaza la evidence
        $this->actingAs(User::where('user_rpe', 'COORD001')->first());

        $response = $this->postJson('/RevisionEvidencias/desaprobar', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe, // PROF001 - dueño
            'feedback' => 'Necesita mejoras en la documentación'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Evidencia marcada como NO APROBADA'
                ]);

        // Verificar status de rechazo por COORDINADOR
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 300,
            'user_rpe' => 'COORD001', // El revisor que rechazó
            'status_description' => 'NO APROBADA',
            'feedback' => 'Necesita mejoras en la documentación'
        ]);

        // Verificar que NO se asignó al siguiente revisor (flujo se detiene en rechazo)
        $this->assertDatabaseMissing('statuses', [
            'evidence_id' => 300,
            'user_rpe' => 'JEFE001',
            'status_description' => 'PENDIENTE'
        ]);

        // Verificar notificación de rechazo al PROFESOR
        $this->assertDatabaseHas('notifications', [
            'evidence_id' => 300,
            'user_rpe' => 'PROF001', // Dueño de la evidence
            'title' => 'Evidencia NO APROBADA'
        ]);
    }

    /** @test */
    public function it_handles_transversal_evidence_workflow()
    {
        // Crear evidence con estándar transversal (va directo a ADMIN)
        $evidence = $this->createWorkflowEvidence(400, 2); // standard_id 2 es transversal

        // Cualquier usuario marca como pendiente debería ir a ADMINISTRADOR
        $this->actingAs(User::where('user_rpe', 'ADMIN001')->first());

        $response = $this->postJson('/RevisionEvidencias/pendiente', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe // PROF001 - dueño
        ]);

        $response->assertStatus(200);

        // Para evidencias transversales, debería asignarse a ADMINISTRADOR directamente
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 400,
            'user_rpe' => 'ADMIN001', // Directo a ADMIN para transversales
            'status_description' => 'PENDIENTE'
        ]);
    }

    /** @test */
    public function it_handles_administrador_approval_flow()
    {
        $evidence = $this->createWorkflowEvidence(500);

        // ADMINISTRADOR aprueba directamente (flujo especial que aprueba a todos)
        $this->actingAs(User::where('user_rpe', 'ADMIN001')->first());

        $response = $this->postJson('/RevisionEvidencias/aprobar', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe, // PROF001 - dueño
            'feedback' => 'Aprobado por administrador'
        ]);

        $response->assertStatus(200);

        // Verificar que el ADMINISTRADOR tiene status APROBADA
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 500,
            'user_rpe' => 'ADMIN001', // El revisor (ADMIN)
            'status_description' => 'APROBADA'
        ]);

        // En flujo de administrador, debería aprobar a todos en la jerarquía automáticamente
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 500,
            'user_rpe' => 'COORD001', // Coordinador aprobado automáticamente
            'status_description' => 'APROBADA'
        ]);

        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 500,
            'user_rpe' => 'JEFE001', // Jefe de área aprobado automáticamente  
            'status_description' => 'APROBADA'
        ]);
    }

    /** @test */
    public function it_marks_evidence_as_pending()
    {
        $evidence = $this->createWorkflowEvidence(600);

        // COORDINADOR marca como pendiente
        $this->actingAs(User::where('user_rpe', 'COORD001')->first());

        $response = $this->postJson('/RevisionEvidencias/pendiente', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe // PROF001 - dueño
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Evidencia marcada como PENDIENTE'
                ]);

        // Verificar status PENDIENTE del COORDINADOR
        $this->assertDatabaseHas('statuses', [
            'evidence_id' => 600,
            'user_rpe' => 'COORD001', // El revisor que marcó como pendiente
            'status_description' => 'PENDIENTE'
        ]);

        // Verificar que NO se creó notificación para estado PENDIENTE
        $this->assertDatabaseMissing('notifications', [
            'evidence_id' => 600,
            'title' => 'Evidencia PENDIENTE'
        ]);
    }
}