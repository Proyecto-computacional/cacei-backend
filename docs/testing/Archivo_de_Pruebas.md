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
php artisan test tests/Integration/

# Ejecutar solo pruebas de Auth
php artisan test tests/Integration/Auth/

# Ejecutar prueba específica
php artisan test tests/Integration/Auth/AuthLoginIntegrationTest.php

# Con reporte de cobertura
php artisan test tests/Integration/Auth/ --coverage-html=coverage/

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
php artisan test tests/Integration/Evidence/

# Ejecutar solo pruebas CRUD
php artisan test tests/Integration/Evidence/EvidenceCrudIntegrationTest.php

# Ejecutar solo pruebas de File Upload
php artisan test tests/Integration/Evidence/FileUploadIntegrationTest.php

# Con reporte de cobertura
php artisan test tests/Integration/Evidence/ --coverage-html=coverage/

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
php artisan test tests/Integration/Workflow/

# Ejecutar solo pruebas de revisión
php artisan test tests/Integration/Workflow/EvidenceRevisionWorkflowTest.php

# Ejecutar solo pruebas de notificaciones
php artisan test tests/Integration/Workflow/NotificationWorkflowTest.php

# Con reporte de cobertura
php artisan test tests/Integration/Workflow/ --coverage-html=coverage/

CI/CD:
# En pipeline
php artisan test --testsuite=integration --filter=Workflow