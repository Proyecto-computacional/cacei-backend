<?php
// tests/Integration/Evidence/Traits/EvidenceTestDataTrait.php

namespace Tests\Integration\Evidence\Traits;

use App\Models\Category;
use App\Models\FrameOfReference;
use App\Models\Section;
use App\Models\User;
use App\Models\Standard;
use App\Models\Accreditation_Process;
use Laravel\Sanctum\Sanctum;

trait EvidenceTestDataTrait
{
    protected $adminUser;
    protected $professorUser;
    protected $coordinatorUser;

    /**
     * Crear datos base necesarios para pruebas de Evidence
     */
    protected function createEvidenceTestData()
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
        // Crear estándares
        $standards = [
            ['standard_id' => 1, 'standard_name' => 'Estándar 1', 'section_id' => 1, 'is_transversal' => false, 'indice' => 1, 'standard_description' => 'Descripción de Estándar 1'],
            ['standard_id' => 2, 'standard_name' => 'Estándar Transversal', 'section_id' => 1, 'is_transversal' => true, 'indice' => 2, 'standard_description' => 'Descripción de Estándar Transversal'],
        ];

        foreach ($standards as $standard) {
            Standard::create($standard);
        }

        // Crear procesos de acreditación
        $processes = [
            ['process_id' => 1, 'process_name' => 'Proceso 2024', 'career_id' => 75, 'frame_id' => 1, 'finished' => false, 'deleted' => false],
            ['process_id' => 2, 'process_name' => 'Proceso 2024', 'career_id' => 83, 'frame_id' => 1, 'finished' => false, 'deleted' => false],
        ];

        foreach ($processes as $process) {
            Accreditation_Process::create($process);
        }

        // Crear usuarios con diferentes roles y guardar referencias
        $this->professorUser = User::create([
            'user_rpe' => 'PROF001',
            'user_name' => 'Profesor Test',
            'user_mail' => 'profesor@uaslp.mx',
            'user_role' => 'PROFESOR',
            'user_area' => '2'
        ]);

        $this->coordinatorUser = User::create([
            'user_rpe' => 'COORD001',
            'user_name' => 'Coordinador Test',
            'user_mail' => 'coordinador@uaslp.mx',
            'user_role' => 'COORDINADOR DE CARRERA',
            'user_area' => '2'
        ]);

        $this->adminUser = User::create([
            'user_rpe' => 'ADMIN001',
            'user_name' => 'Admin Test',
            'user_mail' => 'admin@uaslp.mx',
            'user_role' => 'ADMINISTRADOR',
            'user_area' => '2'
        ]);
    }

    /**
     * Autenticar como administrador (para operaciones globales)
     */
    protected function actingAsAdmin()
    {
        Sanctum::actingAs($this->adminUser);
        return $this->adminUser;
    }

    /**
     * Autenticar como profesor
     */
    protected function actingAsProfessor()
    {
        Sanctum::actingAs($this->professorUser);
        return $this->professorUser;
    }

    /**
     * Autenticar como coordinador
     */
    protected function actingAsCoordinator()
    {
        Sanctum::actingAs($this->coordinatorUser);
        return $this->coordinatorUser;
    }

    /**
     * Payload para crear evidence
     */
    protected function getCreateEvidencePayload()
    {
        return [
            'standard_id' => 1,
            'user_rpe' => 'PROF001',
            'process_id' => 1,
            'due_date' => '2024-12-31'
        ];
    }

    /**
     * Payload para subir archivos
     */
    protected function getFileUploadPayload($evidenceId)
    {
        return [
            'evidence_id' => $evidenceId,
            'justification' => 'Justificación de prueba'
        ];
    }
}