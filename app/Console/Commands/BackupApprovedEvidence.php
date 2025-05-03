<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class BackupApprovedEvidence extends Command
{
    protected $signature = 'backup:approved-evidence';
    protected $description = 'Crea un backup ZIP de archivos de evidencias aprobadas por un administrador';

    public function handle()
    {
        // Obtener las evidencias aprobadas por administradores
        $evidences = DB::table('statuses')
            ->join('users', 'statuses.user_rpe', '=', 'users.user_rpe')
            ->where('status_description', 'aprobado')
            ->where('users.user_role', 'administrador')
            ->pluck('evidence_id')
            ->unique();

        if ($evidences->isEmpty()) {
            $this->info('No hay evidencias aprobadas por administradores.');
            return;
        }

        $zip = new ZipArchive();
        $zipFileName = 'approved_evidences_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/backups/' . $zipFileName);

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            $this->error("No se pudo crear el archivo ZIP.");
            return;
        }

        foreach ($evidences as $evidenceId) {
            $files = DB::table('files')->where('evidence_id', $evidenceId)->get();

            foreach ($files as $file) {
                $filePath = storage_path("app/{$file->file_url}");

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, "{$evidenceId}/{$file->file_name}");
                }
            }
        }

        $zip->close();

        $this->info("Backup creado en: {$zipPath}");
    }
}
