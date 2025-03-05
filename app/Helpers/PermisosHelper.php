<?php
if (!function_exists('iluminarPermiso')) {
    function iluminarPermiso($rol, $permiso): string
    {
        $permisos = [
            'DIRECTIVO' => ['ver'],
            'JEFE DE AREA' => ['ver', 'crear', 'eliminar'],
            'COORDINADOR' => ['ver', 'crear', 'eliminar'],
            'PROFESOR RESPONSABLE' => ['ver', 'crear', 'eliminar'],
            'PROFESOR' => ['ver', 'crear'],
            'TUTOR ACADEMICO' => ['ver', 'crear'],
            'DEPARTAMENTO UNIVERSITARIO' => ['ver', 'crear'],
            'PERSONAL DE APOYO' => ['ver', 'crear'],
        ];

        return in_array($permiso, $permisos[$rol] ?? []) ? 'text-primary' : 'text-muted';
    }
}
