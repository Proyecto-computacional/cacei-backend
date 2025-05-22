<?php

namespace App\Jobs;

use App\Models\Evidence;
use DB;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CvController;
use App\Http\Controllers\GroupController;
use App\Models\Accreditation_Process;

class GenerateAcreditacionZip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $procesoId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $procesoId)
    {
        $this->procesoId = $procesoId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //Log::info("procesoId: " . $this->procesoId);
        $procesoId = $this->procesoId;
        $proceso = Accreditation_Process::find($procesoId);
        $area = $proceso->career->area->area_id;

        $semester;
        $dateProcess = new \DateTime($proceso->end_date);

        //calcular el semestre del proceso
        if($dateProcess->format('n') <= 8 && $dateProcess->format('n') >= 1){
            $semester = ($dateProcess->format('Y') - 1) . "-" . $dateProcess->format('Y') . "/II";
        }else{
            $semester = $dateProcess->format('Y') . "-" . ($dateProcess->format('Y') + 1) . "/I";
        }

        
        // Crear carpeta temporal
        $tempPath = storage_path("app/temp_zips/$procesoId");
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

       //obtener los grupos del area en el semestre del proceso
        $area_groups = GroupController::getGroupsByArea($semester, $area);
        $area_groups_data = json_decode($area_groups->getContent(), true);

        $filesAdded = 0;
        if(isset($area_groups_data['data']['datos'])){
            $unique_rpes = array_unique(array_column($area_groups_data['data']['datos'], 'rpe'));
            foreach($unique_rpes as $rpe){
            $response = CvController::saveCv($rpe, "$tempPath/cv");
            Log::info("Response cv: " . $response);
            $filesAdded++;
            }
        }

        // Paso 1: Obtener evidencias del proceso
        $evidencias = Evidence::where('process_id', $procesoId)->get();

        foreach ($evidencias as $evidencia) {
            // Paso 2: Obtener archivos relacionados con la evidencia
            $archivos = DB::table('files')
                ->where('evidence_id', $evidencia->evidence_id)
                ->get();

            foreach ($archivos as $archivo) {
                $filePath = storage_path("app/public/{$archivo->file_url}");

                if (file_exists($filePath)) {
                    // Paso 3: Extraer nombre y secciones
                    $nombreArchivo = basename($archivo->file_url); // Ej: "1000_1_1_86.pdf"

                    // Suponiendo que el nombre es "Seccion_Subseccion_Resto.pdf"
                    // Por ejemplo: "1_1.1_001-abc123.pdf" o similar
                    $partes = explode('_', $nombreArchivo);

                    $seccion = $partes[0] ?? 'Desconocido';
                    $subseccion = $partes[1] ?? 'Desconocido';
                    $destino = "$tempPath/{$seccion}/{$subseccion}";

                    if (!file_exists($destino)) {
                        mkdir($destino, 0777, true);
                    }

                    // Paso 4: Copiar archivo a la carpeta organizada
                    copy($filePath, "$destino/{$nombreArchivo}");
                    $filesAdded++;
                }
            }
        }

        // Paso 5: Crear el ZIP
        $zip = new ZipArchive;
        $zipPath = storage_path("app/zips/proceso_$procesoId.zip");

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
        }

        // Paso 6: Eliminar carpeta temporal
        File::deleteDirectory($tempPath);

        // No return response, just create the file
        if ($filesAdded === 0) {
            // If no files were added, delete the empty zip
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }
    }
}
