Instalaciones para testing
Verificar lo que YA tienes:
# En tu proyecto Laravel, ejecuta:
php artisan --version
composer show | grep test

Instalaciones esenciales (si faltan):
# Asegurar testing básico
composer require --dev phpunit/phpunit
composer require --dev laravel/dusk

Configurar Dusk (pruebas E2E):
php artisan dusk:install

Archivos de configuración AUTOMÁTICOS(que probablemente se borren ya que pueden cambiar drasticamente segun los mantenimientos que se le den al sistema):
# Laravel ya crea estos archivos automáticamente:
- phpunit.xml
- tests/TestCase.php
- tests/Unit/ExampleTest.php
- tests/Feature/ExampleTest.php
- tests/Browser/ExampleTest.php (con Dusk)

Comandos Seguros para Verificar
SIN riesgo de que fallen pruebas:
# Solo verifica que los comandos existen
php artisan list | grep test
php artisan list | grep dusk

Auth Login Integration
Objetivo
Valida:

Flujo completo de login con API universitaria externa

Creación automática de User + CV para usuarios nuevos

Login exitoso para usuarios existentes

Manejo correcto de errores de la API universitaria

Integración entre User, CV y Areas con foreign keys

NO valida:

UI del frontend (solo backend)

Seguridad avanzada o rate limiting

Performance bajo carga

Cómo Ejecutar
Local:
# Ejecutar todas las pruebas de integración
php artisan test tests/integration/

# Ejecutar solo pruebas de Auth
php artisan test tests/integration/Auth/

# Ejecutar prueba específica
php artisan test tests/integration/Auth/AuthLoginIntegrationTest.php

# Con reporte de cobertura
php artisan test tests/integration/Auth/ --coverage-html=coverage/

CI/CD:
# En pipeline
php artisan test --testsuite=integration --filter=auth

Evidence & File Integration
Objetivo
Valida:

CRUD completo de Evidencias (Create, Read, Update)

Sistema de archivos (Upload, Delete, List)

Relaciones complejas (Evidence → Standard → Process → Career → Area)

Filtrado por rol de usuario (PROFESOR, COORDINADOR, ADMINISTRADOR)

Validaciones de archivos (extensión, tamaño)

Autenticación y permisos con Sanctum

NO valida:

UI del frontend

Sistema de notificaciones

Jobs de backup

Middleware de permisos específicos (se mockean)

Cómo Ejecutar
Local:
# Ejecutar todas las pruebas de Evidence
php artisan test tests/integration/Evidence/

# Ejecutar solo pruebas CRUD
php artisan test tests/integration/Evidence/EvidenceCrudIntegrationTest.php

# Ejecutar solo pruebas de File Upload
php artisan test tests/integration/Evidence/FileUploadIntegrationTest.php

# Con reporte de cobertura
php artisan test tests/integration/Evidence/ --coverage-html=coverage/

CI/CD:
# En pipeline
php artisan test --testsuite=integration --filter=evidence

Evidence Revision Workflow
Objetivo

Valida:

Flujo completo de revisión: PROFESOR → COORDINADOR → JEFE DE ÁREA → ADMINISTRADOR

Cambios de estado: PENDIENTE → APROBADA → NO APROBADA

Asignación automática de siguientes revisores

Notificaciones por cambio de estado

Diferenciación entre estándares normales vs transversales

NO valida:

UI del frontend

Jobs de backup automático

Sistema de reintentos

Flujos Probados:

Flujo	        Estado Inicial	        Acción	                Estado Final	    Notificación
Normal	        -	                    COORD aprueba	        JEFE pendiente	    PROF recibe
Rechazo	        COORD pendiente	        COORD rechaza	        Flujo termina	    PROF recibe
Transversal	    -	                    Cualquiera pendiente    ADMIN pendiente	    -
Admin	        -	                    ADMIN aprueba	        Todos aprobados	    PROF recibe

Datos Críticos Requeridos:
// Estructura mínima para flujo de revisión
Area (con user_rpe de JEFE)
Career (con user_rpe de COORD) 
Evidence (con process → career → area)
Users (PROFESOR, COORDINADOR, JEFE_DE_AREA, ADMINISTRADOR)

Cómo Ejecutar
Local:
# Ejecutar todas las pruebas de workflow
php artisan test tests/integration/Workflow/

# Ejecutar solo pruebas de revisión
php artisan test tests/integration/Workflow/EvidenceRevisionWorkflowTest.php

# Ejecutar solo pruebas de notificaciones
php artisan test tests/integration/Workflow/NotificationWorkflowTest.php

# Con reporte de cobertura
php artisan test tests/integration/Workflow/ --coverage-html=coverage/

CI/CD:
# En pipeline
php artisan test --testsuite=integration --filter=Workflow

Smoke Tests
Objetivo
Qué valida:

Funcionamiento básico de la aplicación Laravel y sus componentes críticos

Disponibilidad de endpoints principales (web y API)

Conectividad a base de datos

Autenticación y autorización básica

Integridad de middlewares (CORS, auth)

Respuesta adecuada de servicios externos mockeados

NO valida:

Lógica de negocio compleja

Flujos completos de usuario

Rendimiento o carga del sistema

Integración con servicios externos reales

Casos edge o validaciones específicas

Cómo ejecutar
Local
# Ejecutar todos los tests de smoke
php artisan test --testsuite=smoke --filter=SmokeTest

# Ejecutar test específico
php artisan test --filter=application_returns_successful_response

# Con cobertura
php artisan test --coverage --min=80 --testsuite=smoke

CI/CD
# En pipeline (ejemplo GitHub Actions)
- name: Run Smoke Tests
  run: |
    php artisan migrate:fresh --seed
    php artisan test --testsuite=Feature --stop-on-failure
      
  env:
    DB_CONNECTION: pgsql
    DB_DATABASE: :memory:
    APP_ENV: testing

Datos de prueba
Factories utilizadas
User::factory()->create(['cv_id' => null]);

Seeds necesarios
Ninguno específico (usa RefreshDatabase)
Factory básica de User

Datos mockeados
Http::fake([
    'servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php' => Http::response([
        'correcto' => false,
        'datos' => []
    ], 200)
]);

Criterios de salida
Cobertura mínima
100% de tests deben pasar

0% de flakiness tolerado

Latencia máxima
Ejecución completa < 30 segundos

Respuesta individual < 2 segundos por test

SLAs de prueba
Disponibilidad aplicación: HTTP 200 en /

Health check: HTTP 200 o 404 (nunca 500)

Database: conexión < 1 segundo

Auth: respuestas < 3 segundos

Errores tolerados
404 aceptables en endpoints opcionales

403 aceptables en autorización

NO se toleran errores 500

Reportes
Rutas de reportes
# Reporte JUnit (CI/CD)
./storage/logs/junit.xml

# Reporte PHPUnit
./reports/test-results.html

# Logs de ejecución
./storage/logs/laravel.log

Formato de reportes
JUnit: ./junit.xml

HTML: ./reports/coverage/

Console: Salida estándar de PHPUnit

Mantenimiento
Rotación de datos
Base de datos se refresca automáticamente (RefreshDatabase)

No requiere limpieza manual

Factories se regeneran en cada ejecución

Responsable
Equipo de Desarrollo: Mantenimiento de tests

DevOps: Ejecución en CI/CD

QA: Validación de criterios

Revisión trimestral
Actualizar endpoints obsoletos

Revisar mocks de servicios externos

Actualizar factories según cambios en modelos

Validar criterios de performance

Checklist de mantenimiento
Todos los endpoints siguen existiendo

Estructura de respuestas JSON no ha cambiado

Permisos de usuario siguen siendo consistentes

Services externos mockeados reflejan API actual

Factories coinciden con estructura de BD actual