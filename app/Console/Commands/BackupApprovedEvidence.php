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
        // Debug: Log the query being executed
        $query = DB::table('statuses')
            ->join('users', 'statuses.user_rpe', '=', 'users.user_rpe')
            ->where('status_description', 'APROBADA')
            ->where('users.user_role', 'ADMINISTRADOR');
        
        $this->info('SQL Query: ' . $query->toSql());
        $this->info('Query Parameters: ' . json_encode($query->getBindings()));

        // Get approved evidence
        $evidences = $query->pluck('evidence_id')->unique();

        $this->info('Number of evidences found: ' . $evidences->count());
        
        if ($evidences->isEmpty()) {
            $this->info('No hay evidencias aprobadas por administradores.');
            return;
        }

        // Check if uploads directory exists
        $uploadsPath = storage_path('app/public/uploads');
        if (!file_exists($uploadsPath)) {
            $this->error("El directorio de uploads no existe: {$uploadsPath}");
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

        $filesAdded = 0;
        foreach ($evidences as $evidenceId) {
            $files = DB::table('files')->where('evidence_id', $evidenceId)->get();
            $this->info("Processing evidence ID: {$evidenceId}, Files found in DB: " . $files->count());

            if ($files->isEmpty()) {
                $this->warn("No files found in database for evidence ID: {$evidenceId}");
                continue;
            }

            foreach ($files as $file) {
                $filePath = storage_path("app/public/{$file->file_url}");
                $this->info("Checking file: {$filePath}");
                $this->info("File URL in DB: {$file->file_url}");

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, "{$evidenceId}/{$file->file_name}");
                    $this->info("Added file to zip: {$file->file_name}");
                    $filesAdded++;
                } else {
                    $this->warn("File not found: {$filePath}");
                    // Check if the directory exists
                    $dirPath = dirname($filePath);
                    if (!file_exists($dirPath)) {
                        $this->warn("Directory does not exist: {$dirPath}");
                    }
                }
            }
        }

        $zip->close();

        if ($filesAdded > 0) {
            $this->info("Backup creado en: {$zipPath}");
            $this->info("Total files added to zip: {$filesAdded}");
        } else {
            $this->error("No se pudieron agregar archivos al backup.");
            // Delete empty zip file
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }
    }
}
