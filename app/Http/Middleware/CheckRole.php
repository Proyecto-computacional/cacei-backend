<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Lista de roles válidos en el sistema
     */
    private const VALID_ROLES = [
        'ADMINISTRADOR',
        'COORDINADOR',
        'JEFE DE AREA',
        'PROFESOR',
        'DIRECTIVO',
        'DEPARTAMENTO UNIVERSITARIO',
        'PERSONAL DE APOYO',
        'CAPTURISTA'
    ];

    /**
     * Tamaño máximo del archivo de log en bytes (5MB)
     */
    private const MAX_LOG_SIZE = 5 * 1024 * 1024;

    /**
     * Número máximo de archivos de log a mantener
     */
    private const MAX_LOG_FILES = 7;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!auth()->check()) {
                $this->logAccess('warning', 'Intento de acceso no autenticado', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);
                return response()->json([
                    'error' => 'No autenticado',
                    'message' => 'Debe iniciar sesión para acceder a esta sección'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = auth()->user();
            $userRole = $user->user_role;

            // Validar que el rol del usuario sea válido
            if (!in_array($userRole, self::VALID_ROLES)) {
                $this->logAccess('error', 'Rol de usuario inválido', [
                    'user_rpe' => $user->user_rpe,
                    'user_role' => $userRole,
                    'ip' => $request->ip()
                ]);
                return response()->json([
                    'error' => 'Rol de usuario inválido',
                    'message' => 'Su cuenta tiene un rol no válido. Por favor contacte al administrador.'
                ], Response::HTTP_FORBIDDEN);
            }

            // Validar que los roles solicitados sean válidos
            $invalidRoles = array_diff($roles, self::VALID_ROLES);
            if (!empty($invalidRoles)) {
                $this->logAccess('error', 'Roles inválidos en middleware', [
                    'invalid_roles' => $invalidRoles,
                    'route' => $request->route()->getName()
                ]);
                return response()->json([
                    'error' => 'Configuración de roles inválida',
                    'message' => 'Error interno del sistema'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Verificar si el rol del usuario está en la lista de roles permitidos
            if (!in_array($userRole, $roles)) {
                $this->logAccess('warning', 'Acceso no autorizado', [
                    'user_rpe' => $user->user_rpe,
                    'user_role' => $userRole,
                    'required_roles' => $roles,
                    'ip' => $request->ip()
                ]);
                return response()->json([
                    'error' => 'Acceso denegado',
                    'message' => 'No tienes permisos para acceder a este recurso'
                ], Response::HTTP_FORBIDDEN);
            }

            // Solo registrar accesos exitosos para roles administrativos
            if (in_array($userRole, ['ADMINISTRADOR'])) {
                $this->logAccess('info', 'Acceso autorizado', [
                    'user_rpe' => $user->user_rpe,
                    'user_role' => $userRole,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);
            }

            return $next($request);
        } catch (\Exception $e) {
            $this->logAccess('error', 'Error en middleware', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'error' => 'Error interno del servidor',
                'message' => 'Ha ocurrido un error al verificar los permisos'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Registra el acceso en el archivo de logs específico
     */
    private function logAccess(string $level, string $message, array $context = []): void
    {
        try {
            $logFile = storage_path('logs/access-' . now()->format('Y-m-d') . '.log');

            // Verificar y rotar logs si es necesario
            $this->rotateLogsIfNeeded($logFile);

            $logMessage = sprintf(
                "[%s] [%s] %s - %s",
                now()->format('Y-m-d H:i:s'),
                strtoupper($level),
                $message,
                json_encode($context, JSON_UNESCAPED_UNICODE)
            );

            file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
            // Si falla el logging, no interrumpimos la ejecución
            Log::error('Error al escribir en el log de acceso: ' . $e->getMessage());
        }
    }

    /**
     * Rota los archivos de log si es necesario
     */
    private function rotateLogsIfNeeded(string $currentLogFile): void
    {
        if (!file_exists($currentLogFile)) {
            return;
        }

        // Verificar tamaño del archivo actual
        if (filesize($currentLogFile) >= self::MAX_LOG_SIZE) {
            $this->rotateLogs();
        }
    }

    /**
     * Rota los archivos de log
     */
    private function rotateLogs(): void
    {
        $logPath = storage_path('logs');
        $files = glob($logPath . '/access-*.log');
        
        // Ordenar archivos por fecha de modificación (más antiguos primero)
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Eliminar archivos antiguos si exceden el límite
        while (count($files) >= self::MAX_LOG_FILES) {
            $oldestFile = array_shift($files);
            if (file_exists($oldestFile)) {
                unlink($oldestFile);
            }
        }
    }
}