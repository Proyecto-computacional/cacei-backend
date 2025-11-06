<?php
// tests/Integration/Auth/AuthLoginIntegrationTest.php

namespace Tests\Integration\Auth;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Cv;
use App\Models\Area;

class AuthLoginIntegrationTest extends TestCase
{
    use RefreshDatabase;
    

    /** @test */
    public function it_logs_in_successfully_with_new_user_and_creates_cv()
    {
        // Mock de la API universitaria - respuesta exitosa
        Http::fake([
            'servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php' => Http::response([
                'correcto' => true,
                'datos' => [
                    [
                        'rpe' => '123456',
                        'nombre' => 'Profesor Prueba',
                        'correo' => 'profesor@uaslp.mx',
                        'cargo' => 'PROFESOR',
                        'cve_area' => '2' // Área de Ciencias de la Computación
                    ]
                ]
            ], 200)
        ]);

        // Verificar que NO existe el usuario antes del login
        $this->assertDatabaseMissing('users', ['user_rpe' => '123456']);
        $this->assertDatabaseMissing('cvs', ['professor_number' => 123456]);

        // Intentar login con usuario que NO existe en BD local
        $response = $this->postJson('/login', [
            'rpe' => '123456',
            'password' => 'password123'
        ]);

        // Verificar respuesta HTTP
        $response->assertStatus(200);
        $response->assertJson([
            'correct' => true,
            'message' => 'Login exitoso'
        ]);

        // Verificar que se creó el usuario en la base de datos
        $this->assertDatabaseHas('users', [
            'user_rpe' => '123456',
            'user_mail' => 'profesor@uaslp.mx',
            'user_role' => 'PROFESOR',
            'user_name' => 'Profesor Prueba',
            'user_area' => '2' // Debe coincidir con el area_id existente
        ]);

        // Verificar que se creó el CV con la relación correcta
        $this->assertDatabaseHas('cvs', [
            'professor_number' => 123456,
            'professor_name' => 'Profesor Prueba',
            'actual_position' => 'PROFESOR'
        ]);

        // Verificar la relación entre User y CV
        $user = User::where('user_rpe', '123456')->first();
        $cv = Cv::where('professor_number', 123456)->first();
        
        $this->assertNotNull($user, 'User should be created');
        $this->assertNotNull($cv, 'CV should be created');
        $this->assertEquals($user->cv_id, $cv->cv_id, 'User should reference CV');
    }

    /** @test */
    public function it_logs_in_successfully_with_existing_user()
    {
        // Primero crear un CV existente
        $cv = Cv::create([
            'cv_id' => 1,
            'professor_number' => 123456,
            'professor_name' => 'Profesor Existente',
            'actual_position' => 'PROFESOR'
        ]);

        // Crear usuario existente
        $user = User::create([
            'user_rpe' => '123456',
            'user_name' => 'Profesor Existente',
            'user_mail' => 'viejo_email@uaslp.mx',
            'user_role' => 'PROFESOR',
            'user_area' => '2', // Área existente
            'cv_id' => $cv->cv_id
        ]);

        // Mock de API universitaria
        Http::fake([
            'servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php' => Http::response([
                'correcto' => true,
                'datos' => [
                    [
                        'rpe' => '123456',
                        'nombre' => 'Profesor Existente',
                        'correo' => 'viejo_email@uaslp.mx',
                        'cargo' => 'PROFESOR', // MISMO rol
                        'cve_area' => '2' // MISMA área
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/login', [
            'rpe' => '123456',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJson(['correct' => true]);

        // Verificar que accedio
        $this->assertDatabaseHas('users', [
            'user_rpe' => '123456',
            'user_role' => 'PROFESOR', 
            'user_mail' => 'viejo_email@uaslp.mx',
            'user_area' => '2' 
        ]);
    }

    /** @test */
    public function it_fails_login_with_invalid_university_credentials()
    {
        Http::fake([
            'servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php' => Http::response([
                'correcto' => false,
                'datos' => []
            ], 200)
        ]);

        $response = $this->postJson('/login', [
            'rpe' => '123456',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'correct' => false,
                    'message' => 'Error en rpe o contra'
                ]);

        $this->assertDatabaseMissing('users', ['user_rpe' => '123456']);
    }
}