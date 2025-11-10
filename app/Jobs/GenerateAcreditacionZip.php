<?php

namespace App\Jobs;

use App\Http\Controllers\AccreditationProcessController;
use App\Models\Evidence;
use DB;
use Illuminate\Support\Facades\File;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use function Psy\debug;

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
        ////Log::info("procesoId: " . $this->procesoId);
        $includeCV = ["categoryIndex" => 5, "sectionIndex" => 1, "standardIndex" => 1];
        $procesoId = $this->procesoId;
       $proceso = Accreditation_Process::with([
            'frame.categories.sections.standards.evidences.files',
            'frame.categories.sections.standards.evidences.lastAdminStatus'
        ])->find($procesoId);

        $area = $proceso->career->area->area_id;
        
        $filesAdded = 0;

        //Log::debug("Proceso:", [$proceso]);
        

        $basePath = "temp_zips/{$procesoId}"; 
        //Log::debug("basePath {$basePath}");
        foreach ($proceso->frame->categories as $category) {
            $categoryPath = "{$basePath}/{$category->indice}.{$category->category_name}";
            Storage::makeDirectory($categoryPath);
            //Log::debug("Category folder created: {$categoryPath}");

            foreach ($category->sections as $section) {
                $sectionPath = "{$categoryPath}/{$category->indice}.{$section->indice}.{$section->section_name}";
                Storage::makeDirectory($sectionPath);
                //Log::debug("Section folder created: {$sectionPath}");

                foreach ($section->standards as $standard) {
                    $charIndex = $this->numToLetter($standard->indice);
                    $standardPath = "{$sectionPath}/{$category->indice}.{$section->indice}.{$charIndex}.{$standard->standard_name}";
                    //Log::debug("standard path: {$standardPath}");
                    //Es el criterio de los cvs de los profesores?
                    if($category->indice === $includeCV["categoryIndex"] && $section->indice === $includeCV["sectionIndex"] && $standard->indice === $includeCV["standardIndex"]){
                        //Generar carpeta de cvs
                        Storage::makeDirectory($standardPath);
                        //Log::debug("CV Standard folder created: {$standardPath}");

                        //Obtener los rpes de los profesores de los que se necesita el CV para el proceso.
                        $rpesByArea = AccreditationProcessController::getCVsProcess($procesoId);
                        $rpesByArea = $rpesByArea->getData(true);
                        $index = 0;
                        $areas = [$proceso->career->area->area_name, "Departmaneto de Físico - Matemáticas", "Área de Formación Humanística"]; 
                        foreach($rpesByArea as $area){
                            //dividir cvs en carpetas por cada area
                            //Log::debug("i {$index}");
                            Storage::makeDirectory("{$standardPath}/{$areas[$index]}");
                            $rpes = $area;
                            //Log::debug("Area for cvs:", $area);
                            foreach($rpes as $rpe){
                                if($rpe !== null){
                                    $outpath = storage_path("app/{$standardPath}/{$areas[$index]}/");
                                    //Log::debug("cv Outhpath {$outpath}");
                                    $response = CvController::saveCv($rpe, $outpath);
                                    ////Log::debug("cv response {$response}");
                                    $filesAdded++;
                                }
                            }
                            $index++;
                        }
                    }
                    foreach ($standard->evidences as $evidence) {
                        if($evidence->lastAdminStatus){
                            Log::debug($evidence->lastAdminStatus);
                            if($evidence->lastAdminStatus->status_description === 'APROBADA'){
                                foreach($evidence->files as $file){
                                        $fileExtension = pathinfo($file->file_url, PATHINFO_EXTENSION);
                                        $outpath = storage_path("app/{$standardPath}.{$fileExtension}");
                                        //Log::debug("file Outhpath {$outpath}");
                                        $filePath = storage_path("app/public/{$file->file_url}");
                                        //Log::debug("file path {$filePath}");
                                        $response = copy($filePath, $outpath);
                                        //Log::debug("file copy response {$response}");
                                        $filesAdded++;
                                }
                            }
                        $pdf = Pdf::loadHTML($evidence->justification);
                        $path = storage_path("app/{$standardPath}_justificación.pdf");
                        $pdf->save($path);
                        }
                    }
                }
            }
        }
        
        // Crear el ZIP
        $zip = new ZipArchive;
        $zipName = Str::slug($proceso->process_name, '_') . '.zip';
        $zipPath = storage_path("app/zips/{$zipName}");
        $tempPath = storage_path("app/{$basePath}/");
        //Log::debug("TempPath {$tempPath}");
         //Log::debug("Directorio ZIP creado: " . dirname($zipPath));
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0777, true);
            //Log::debug("Directorio ZIP creado: " . dirname($zipPath));
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            // Normalizar antes del foreach
            $tempPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $tempPath);
            $tempPath = rtrim($tempPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            //Log::debug("TempPath normalizado: {$tempPath}");

            foreach ($files as $file) {
                    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file->getRealPath());
                    $relativePath = substr($filePath, strlen($tempPath));
                    $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);
                if (!$file->isDir()) {
                    //incluir carpetas con archivo
                    $zip->addFile($filePath, $relativePath);
                }else{
                    //tambien incluir carpetas sin archivo para respetar la estructura
                    $zip->addEmptyDir($relativePath);
                }
            }
            $zip->close();
        }else{
             //Log::debug("Zip not open");
        }

        ////Log::debug("ZipPath {$zipPath}");

        //Eliminar carpeta temporal
        File::deleteDirectory($tempPath);

        // Si no se agrego un solo archivo, eliminar zip para manejar respuesta desde el controlador
        if ($filesAdded === 0) {
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        }
    }

    //Pasar indices de criterios a indices alfabeticos
    private function numToLetter($num) {
        return chr(96 + $num);
    }
}
