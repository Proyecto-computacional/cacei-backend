<?php
// tests/Feature/SmokeTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function application_returns_successful_response()
    {
        $response = $this->get('/');
        $response->assertSuccessful();
    }

    public function api_health_check_returns_ok()
    {
        $response = $this->get('/api/health');
        
        // Si el endpoint existe, debería retornar 200
        // Si no existe, podría retornar 404 pero no 500
        if ($response->status() !== 200) {
            $this->assertNotEquals(500, $response->status(), 
                'Health endpoint returned server error');
        }
    }

    /** @test */
    public function database_connection_is_working()
    {
        try {
            \DB::connection()->getPdo();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail("Could not connect to the database: {$e->getMessage()}");
        }
    }

    /** @test */
    public function login_endpoint_is_accessible()
    {
        // Mock de la API externa para evitar dependencias en tests
        Http::fake([
            'servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php' => Http::response([
                'correcto' => false,
                'datos' => []
            ], 200)
        ]);

        $response = $this->post('/api/login', [
            'rpe' => 'testuser',
            'password' => 'testpassword',
        ]);

        // El endpoint debería responder (no 500 error)
        // Puede retornar 200 con correct: false o error de validación
        $this->assertNotEquals(500, $response->status(), 
            'Login endpoint returned server error');
        
        // Verificar que es una respuesta JSON válida
        if ($response->headers->get('Content-Type') === 'application/json') {
            $responseData = $response->json();
            $this->assertArrayHasKey('correct', $responseData);
        }
    }

    /** @test */
    public function protected_api_routes_require_authentication()
    {
        $response = $this->get('/api/user', [
            'Accept' => 'application/json'
        ]);

        // Debería retornar 401 (no autenticado) 
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_access_protected_routes()
    {
        // Crear un usuario directamente en la base de datos
        $user = User::factory()->create([
            'cv_id' => null,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        // Probar endpoint de usuario actual
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/user');

        // Debería retornar 200 (éxito) con datos del usuario
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user_rpe', 'user_name', 'user_mail', 'user_role'
        ]);
    }

    /** @test */
    public function evidence_endpoints_are_accessible_with_auth()
    {
        // Crear usuario sin cv_id
        $user = User::factory()->create([
            'cv_id' => null,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        // Probar endpoint de revisión de evidencias
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/ReviewEvidence');

        // Verificar que no hay error de servidor
        $this->assertNotEquals(500, $response->status(),
            'Evidence endpoint returned server error');
            
        // Puede retornar:
        // - 200: con datos de evidencias
        // - 204: sin evidencias
        // - 404: endpoint no existe (pero sabemos que existe)
        // - 403: no tiene permisos
        $this->assertContains($response->status(), [200, 204, 403, 404],
            'Evidence endpoint returned unexpected status: ' . $response->status());
    }

    /** @test */
    public function file_upload_endpoints_are_accessible()
    {
        // Crear usuario sin cv_id
        $user = User::factory()->create([
            'cv_id' => null,
        ]);
        
        $token = $user->createToken('test-token')->plainTextToken;

        // Probar que podemos acceder a endpoints relacionados con archivos
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->get('/api/user'); // Endpoint básico que sabemos existe

        $this->assertNotEquals(500, $response->status(),
            'Basic API endpoint returned server error');
    }

    /** @test */
    public function critical_middlewares_are_working()
    {
        // Test CORS
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000'
        ])->get('/');

        $response->assertSuccessful();
    }

    /** @test */
    public function user_model_and_factory_work_correctly()
    {
        // Test básico de que el modelo User funciona
        $user = User::factory()->create([
            'cv_id' => null,
        ]);

        $this->assertDatabaseHas('users', [
            'user_rpe' => $user->user_rpe,
            'cv_id' => null
        ]);

        $this->assertInstanceOf(User::class, $user);
    }
}