<?php
// tests/Integration/Auth/Traits/AuthTestDataTrait.php

namespace Tests\Integration\Auth\Traits;

trait AuthTestDataTrait
{
    /**
     * Datos de usuario válido para la API universitaria
     */
    protected function getValidUniversityUserData()
    {
        return [
            'correcto' => true,
            'datos' => [
                [
                    'rpe' => '123456',
                    'nombre' => 'Profesor Prueba',
                    'correo' => 'profesor@uaslp.mx',
                    'cargo' => 'Profesor',
                    'cve_area' => 'MAT'
                ]
            ]
        ];
    }

    /**
     * Datos de usuario inválido para la API universitaria
     */
    protected function getInvalidCredentialsData()
    {
        return [
            'correcto' => false,
            'datos' => []
        ];
    }

    /**
     * Datos de usuario con diferente cargo
     */
    protected function getDifferentRoleUserData()
    {
        return [
            'correcto' => true,
            'datos' => [
                [
                    'rpe' => '123456',
                    'nombre' => 'Profesor Prueba',
                    'correo' => 'nuevo_email@uaslp.mx', // Email diferente
                    'cargo' => 'Coordinador', // Cargo diferente
                    'cve_area' => '2'
                ]
            ]
        ];
    }

    /**
     * Datos de usuario con diferente área
     */
    protected function getDifferentAreaUserData()
    {
        return [
            'correcto' => true,
            'datos' => [
                [
                    'rpe' => '123456',
                    'nombre' => 'Profesor Prueba',
                    'correo' => 'profesor@uaslp.mx',
                    'cargo' => 'Profesor',
                    'cve_area' => '3' // Área Civil
                ]
            ]
        ];
    }

    /**
     * Payload de login válido
     */
    protected function getValidLoginPayload()
    {
        return [
            'rpe' => '123456',
            'password' => 'password123'
        ];
    }

     /**
     * Payload de login inválido (campos vacíos)
     */
    protected function getInvalidLoginPayload()
    {
        return [
            'rpe' => '',
            'password' => ''
        ];
    }
}