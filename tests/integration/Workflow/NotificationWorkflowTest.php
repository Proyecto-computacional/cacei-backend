<?php
// tests/Integration/Workflow/NotificationWorkflowTest.php

namespace Tests\Integration\Workflow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Evidence;
use App\Models\Notification;
use App\Models\User;
use Tests\Integration\Workflow\Traits\RevisionWorkflowTrait;

class NotificationWorkflowTest extends TestCase
{
    use RefreshDatabase, RevisionWorkflowTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRevisionWorkflowData();
    }

    /** @test */
    public function it_creates_notification_on_evidence_approval()
    {
        $evidence = $this->createWorkflowEvidence(700);

        // COORDINADOR aprueba
        $this->actingAs(User::where('user_rpe', 'COORD001')->first());

        $response = $this->postJson('/RevisionEvidencias/aprobar', [
            'evidence_id' => $evidence->evidence_id,
            'user_rpe' => $evidence->user_rpe,
            'feedback' => 'Excelente trabajo'
        ]);

        $response->assertStatus(200);

        // Verificar notificación creada
        $this->assertDatabaseHas('notifications', [
            'evidence_id' => 700,
            'user_rpe' => 'PROF001', // Para el profesor
            'title' => 'Evidencia APROBADA',
            'description' => 'Tu evidencia ha sido marcada como APROBADA con el siguiente comentario: Excelente trabajo',
            'seen' => false,
            'pinned' => false
        ]);
    }

    /** @test */
    public function it_retrieves_notifications_for_user()
    {
        $evidence1 = $this->createWorkflowEvidence(700);
        $evidence2 = $this->createWorkflowEvidence(800);
        // Crear notificaciones de prueba
        Notification::create([
            'notification_id' => 1,
            'title' => 'Test Notification 1',
            'evidence_id' => $evidence1->evidence_id,
            'notification_date' => now(),
            'user_rpe' => 'PROF001',
            'reviser_id' => 'COORD001',
            'description' => 'Test description 1',
            'seen' => false,
            'pinned' => false
        ]);

        Notification::create([
            'notification_id' => 2, 
            'title' => 'Test Notification 2',
            'evidence_id' => $evidence2->evidence_id,
            'notification_date' => now(),
            'user_rpe' => 'PROF001',
            'reviser_id' => 'COORD001',
            'description' => 'Test description 2',
            'seen' => true,
            'pinned' => true
        ]);

        $this->actingAs(User::where('user_rpe', 'PROF001')->first());

        $response = $this->getJson('/Notificaciones?user_rpe=PROF001');

        $response->assertStatus(200)
                ->assertJsonStructure([[
                    'notification_id',
                    'title',
                    'description', 
                    'seen',
                    'pinned',
                    'starred',
                    'notification_date',
                    'evidence',
                    'reviser'
                ]]);

        $notifications = $response->json();
        $this->assertCount(2, $notifications);
    }

    /** @test */
    public function it_toggles_notification_favorite_status()
    {
        $evidence = $this->createWorkflowEvidence(700);

        $notification = Notification::create([
            'notification_id' => 10,
            'title' => 'Test Notification',
            'evidence_id' => $evidence->evidence_id,
            'notification_date' => now(),
            'user_rpe' => 'PROF001',
            'reviser_id' => 'COORD001',
            'description' => 'Test description',
            'seen' => false,
            'pinned' => false,
            'starred' => false
        ]);

        $this->actingAs(User::where('user_rpe', 'PROF001')->first());

        $response = $this->putJson('/Notificaciones/favorite', [
            'notification_id' => 10
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Estado de favorito actualizado'
                ]);

        // Verificar que se cambió el estado
        $this->assertDatabaseHas('notifications', [
            'notification_id' => 10,
            'starred' => true
        ]);
    }

    /** @test */
    public function it_toggles_notification_pinned_status()
    {
        $evidence = $this->createWorkflowEvidence(700);

        $notification = Notification::create([
            'notification_id' => 20,
            'title' => 'Test Notification',
            'evidence_id' => $evidence->evidence_id,
            'notification_date' => now(),
            'user_rpe' => 'PROF001',
            'reviser_id' => 'COORD001',
            'description' => 'Test description',
            'seen' => false,
            'pinned' => false
        ]);

        $this->actingAs(User::where('user_rpe', 'PROF001')->first());

        $response = $this->putJson('/Notificaciones/pinned', [
            'notification_id' => 20
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Estado de fijado actualizado'
                ]);

        $this->assertDatabaseHas('notifications', [
            'notification_id' => 20,
            'pinned' => true
        ]);
    }

    /** @test */
    public function it_toggles_notification_seen_status()
    {
        $evidence = $this->createWorkflowEvidence(700);

        $notification = Notification::create([
            'notification_id' => 30,
            'title' => 'Test Notification',
            'evidence_id' => $evidence->evidence_id,
            'notification_date' => now(),
            'user_rpe' => 'PROF001',
            'reviser_id' => 'COORD001',
            'description' => 'Test description',
            'seen' => false
        ]);

        $this->actingAs(User::where('user_rpe', 'PROF001')->first());

        $response = $this->putJson('/Notificaciones/deleted', [
            'notification_id' => 30
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Notificación eliminada'
                ]);

        $this->assertDatabaseHas('notifications', [
            'notification_id' => 30,
            'seen' => true
        ]);
    }
}