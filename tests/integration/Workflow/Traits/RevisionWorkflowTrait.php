<?php
// tests/Integration/Workflow/Traits/RevisionWorkflowTrait.php

namespace Tests\Integration\Workflow\Traits;

use App\Models\Area;
use App\Models\Career;
use App\Models\Category;
use App\Models\FrameOfReference;
use App\Models\Section;
use App\Models\User;
use App\Models\Standard;
use App\Models\Accreditation_Process;
use App\Models\Evidence;
use Laravel\Sanctum\Sanctum;

trait RevisionWorkflowTrait
{
    protected $adminUser;
    protected $professorUser;
    protected $coordinatorUser;
    protected $jefeAreaUser;
    /**
     * Crear estructura completa para pruebas de flujo de revisión
     */
    protected function createRevisionWorkflowData()
    {
        // Crear marco
        $frames = [
            ['frame_id' => 1, 'frame_name' => 'Marco de prueba'],
        ];

        foreach ($frames as $frame) {
            FrameOfReference::create($frame);
        }

        //Crear categoria
        $categories = [
            ['category_id' => 1, 'category_name' => 'Categoria 1', 'frame_id' => 1, 'indice' => 1],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        //Crear indices
        $sections = [
            ['section_id' => 1, 'section_name' => 'Indice 1', 'category_id' => 1, 'indice' => 1, 'section_description' => 'Descripción del indicador 1', 'is_standard' => false],
        ]; 

        foreach ($sections as $section) {
            Section::create($section);
        }

        // Crear estándares (normal y transversal)
        $standards = [
            [
                'standard_id' => 1, 
                'standard_name' => 'Estándar Normal', 
                'section_id' => 1, 
                'is_transversal' => false,
                'indice' => 1, 
                'standard_description' => 'Descripción de Estándar Normal'
            ],
            [
                'standard_id' => 2, 
                'standard_name' => 'Estándar Transversal', 
                'section_id' => 1, 
                'is_transversal' => true,
                'indice' => 2, 
                'standard_description' => 'Descripción de Estándar Transversal'
            ],
        ];

        foreach ($standards as $standard) {
            Standard::create($standard);
        }

        // Crear procesos de acreditación
        $processes = [
            [
                'process_id' => 1, 
                'process_name' => 'Proceso 2024', 
                'career_id' => 75, 
                'frame_id' => 1,
                'finished' => false, 
                'deleted' => false
            ],
        ];

        foreach ($processes as $process) {
            Accreditation_Process::create($process);
        }

        // Crear usuarios con diferentes roles para el flujo
        $this->professorUser = User::create(
            // Profesor que crea la evidencia
            [
                'user_rpe' => 'PROF001',
                'user_name' => 'Profesor Test',
                'user_mail' => 'profesor@uaslp.mx',
                'user_role' => 'PROFESOR',
                'user_area' => '2'
            ]);
            // Coordinador de carrera
        $this->coordinatorUser = User::create(
            [
                'user_rpe' => 'COORD001',
                'user_name' => 'Coordinador Software',
                'user_mail' => 'coord_software@uaslp.mx',
                'user_role' => 'COORDINADOR DE CARRERA',
                'user_area' => '2'
            ]);
        $this->jefeAreaUser = User::create(
            // Jefe de área
            [
                'user_rpe' => 'JEFE001',
                'user_name' => 'Jefe Area Computación',
                'user_mail' => 'jefe_computacion@uaslp.mx',
                'user_role' => 'JEFE DE AREA',
                'user_area' => '2'
            ]);
        $this->adminUser = User::create(
            // Administrador
            [
                'user_rpe' => 'ADMIN001',
                'user_name' => 'Admin Sistema',
                'user_mail' => 'admin@uaslp.mx',
                'user_role' => 'ADMINISTRADOR',
                'user_area' => '2'
            ]);

        // Asignar jefe al área
        Area::where('area_id', '2')->update(['user_rpe' => 'JEFE001']);

        //Asignar coordinador de carrera
        Career::where('career_id', 75)->update(['user_rpe' => 'COORD001']);
    }

    protected function actingAsUser($userRpe)
    {
        $user = User::where('user_rpe', $userRpe)->first();
        $this->currentUser = $user;
        Sanctum::actingAs($user);
        return $user;
    }

    /**
     * Crear evidence para pruebas de flujo
     */
    protected function createWorkflowEvidence($evidenceId, $standardId = 1, $userRpe = 'PROF001')
    {
        return Evidence::create([
            'evidence_id' => $evidenceId,
            'standard_id' => $standardId,
            'user_rpe' => $userRpe,
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ]);
    }

    /**
     * Payload para aprobar/desaprobar evidence
     */
    protected function getRevisionPayload($evidenceId, $ownerRpe, $feedback = null)
    {
        return [
            'evidence_id' => $evidenceId,
            'user_rpe' => $ownerRpe, // Dueño de la evidence (para notificación)
            'feedback' => $feedback,
            'reviser_rpe' => null // Se usará el usuario autenticado como revisor
        ];
    }
}