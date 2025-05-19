<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Cv;
use App\Models\Education;
use App\Models\TeacherTraining;
use App\Models\DisciplinaryUpdate;
use App\Models\AcademicManagement;
use App\Models\AcademicProduct;
use App\Models\LaboralExperience;
use App\Models\EngineeringDesign;
use App\Models\ProfessionalAchievement;
use App\Models\Participation;
use App\Models\Award;
use App\Models\ContributionToPe;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class CvController extends Controller
{
    public function index(Request $request) {
        $user = User::where('user_rpe', $request->user_rpe)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $cv = Cv::where('cv_id', $user->cv_id)->first();
        return response()->json($cv, 200);
    }

    public function show($id) {
        $cv = Cv::find($id);
        if (!$cv) return response()->json(['message' => 'No encontrado'], 404);
        return response()->json($cv, 200);
    }

    public function store(Request $request) {
        $cv = Cv::create($request->all());
        return response()->json($cv, 201);
    }

    public function update(Request $request, $id) {
        $cv = Cv::find($id);
        if (!$cv) return response()->json(['message' => 'No encontrado'], 404);
        $cv->update($request->all());
        return response()->json($cv, 200);
    }

    public function destroy($id) {
        $cv = Cv::find($id);
        if (!$cv) return response()->json(['message' => 'No encontrado'], 404);
        $cv->delete();
        return response()->json(['message' => 'Eliminado correctamente'], 200);
    }

    public function generateWord($user_rpe)
    {
        $user = User::where('user_rpe', $user_rpe)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $cv = Cv::where('cv_id', $user->cv_id)->first();
        if (!$cv) {
            return response()->json(['message' => 'CV no encontrado'], 404);
        }

        $educations = Education::where('cv_id', $cv->cv_id)->orderByDesc('obtained_year')->get();
        $teacher_training = TeacherTraining::where('cv_id', $cv->cv_id)->orderByDesc('obtained_year')->get();
        $disciplinary_update = DisciplinaryUpdate::where('cv_id', $cv->cv_id)->orderByDesc('year_certification')->get();
        $academic_management = AcademicManagement::where('cv_id', $cv->cv_id)->orderByDesc('start_date')->get();
        $academic_product = AcademicProduct::where('cv_id', $cv->cv_id)->orderByDesc('academic_product_number')->get();
        $laboral_experience = LaboralExperience::where('cv_id', $cv->cv_id)->orderByDesc('start_date')->get();
        $engineering_design = EngineeringDesign::where('cv_id', $cv->cv_id)->get();
        $professional_achievement = ProfessionalAchievement::where('cv_id', $cv->cv_id)->get();
        $participation = Participation::where('cv_id', $cv->cv_id)->get();
        $award = Award::where('cv_id', $cv->cv_id)->get();
        $contribution_to_pe = ContributionToPe::where('cv_id', $cv->cv_id)->get()->first();

        // Cargar plantilla
        $templatePath = storage_path('app/templates/Cedula_CV.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['message' => 'Plantilla no encontrada'], 500);
        }

        $template = new TemplateProcessor($templatePath);


        // Insertar valores
        $nombres = explode(' ', $cv->professor_name);
        $template->setValue('paterno', $nombres[0] ?? ''); 
        $template->setValue('materno', $nombres[1] ?? '');
        $template->setValue('nombre', implode(' ', array_slice($nombres, 2)) ?? '');
        $template->setValue('edad', $cv->age);
        $template->setValue('fecha', $cv->birth_date);

        if ($cv->actual_position=="PTC")
		{
			$nomb="Profesor de Tiempo Completo";
		}
		else if ($cv->actual_position=="PMT")
		{
			$nomb="Profesor de Medio Tiempo";
		}
		else if ($cv->actual_position=="PHC")
		{
			 $nomb="Profesor Hora Clase";
		}
		else if ($cv->actual_position=="TA")
		{
			$nomb="Técnico Académico";
		}
		else if ($cv->actual_position=="LAB")
		{
			$nomb="Laboratorista";
		}
    else
        {
            $nomb = $cv->actual_position;
        }
        $template->setValue('nombramiento', $nomb);
        $template->setValue('anti', $cv->duration);

        $licenciaturas = $educations->where('degree_obtained', 'L');
        $especialidades = $educations->where('degree_obtained', 'E');
        $maestrias = $educations->where('degree_obtained', 'M');
        $doctorados = $educations->where('degree_obtained', 'D');

        EducationController::fillEducationSection($template, 'L', 'lic', $licenciaturas);
        EducationController::fillEducationSection($template, 'E', 'esp', $especialidades);
        EducationController::fillEducationSection($template, 'M', 'mas', $maestrias);
        EducationController::fillEducationSection($template, 'D', 'doc', $doctorados);

        if ($teacher_training->isNotEmpty()) {
            $template->cloneRow('capacitacionId', $teacher_training->count());
        
            foreach ($teacher_training->values() as $i => $training) {
                $index = $i + 1;
        
                $template->setValue("capacitacionId#$index", $training->title_certification);
                $template->setValue("insC#$index", $training->institution_country ?? '');
                $template->setValue("obtC#$index", $training->obtained_year);
                $template->setValue("horasC#$index", $training->hours);
            }
        } else {
            $template->setValue("capacitacionId", '');
            $template->setValue("insC", '');
            //$template->setValue("paisC", '');
            $template->setValue("obtC", '');
            $template->setValue("horasC", '');
        }

        if ($disciplinary_update->isNotEmpty()) {
            $template->cloneRow('actualizacionId', $disciplinary_update->count());
        
            foreach ($disciplinary_update->values() as $i => $update) {
                $index = $i + 1;
        
                $template->setValue("actualizacionId#$index", $update->title_certification);
                $template->setValue("insA#$index", $update->institution_country ?? '');
                $template->setValue("obtA#$index", $update->year_certification);
                $template->setValue("horasA#$index", $update->hours);
            }
        } else {
            $template->setValue("actualizacionId", '');
            $template->setValue("insA", '');
            //$template->setValue("paisA", '');
            $template->setValue("obtA", '');
            $template->setValue("horasA", '');
        }

        if ($academic_management->isNotEmpty()) {
            $template->cloneRow('gestionId', $academic_management->count());
        
            foreach ($academic_management->values() as $i => $management) {
                $index = $i + 1;
        
                $template->setValue("gestionId#$index", $management->job_position);
                $template->setValue("insG#$index", $management->institution ?? '');
                $template->setValue("deG#$index", $management->start_date);
                $template->setValue("aG#$index", $management->end_date);
            }
        } else {
            $template->setValue("gestionId", '');
            $template->setValue("insG", '');
            $template->setValue("deG", '');
            $template->setValue("aG", '');
        }

        if ($academic_product->isNotEmpty()) {
            $template->cloneRow('userId', $academic_product->count());
        
            foreach ($academic_product->values() as $i => $product) {
                $index = $i + 1;
        
                $template->setValue("userId#$index", $product->academic_product_number);
                $template->setValue("des#$index", $product->description);
                //$template->setValue("periP#$index", $product->period);
                //El template original valida que haya un año
            }
        } else {
            $template->setValue("userId", '');
            $template->setValue("productoId", '');
            $template->setValue("des", '');
            //$template->setValue("periP", '');
        }

        if ($laboral_experience->isNotEmpty()) {
            $template->cloneRow('actividadId', $laboral_experience->count());
        
            foreach ($laboral_experience->values() as $i => $experience) {
                $index = $i + 1;
        
                $template->setValue("actividadId#$index", $experience->position);
                $template->setValue("orgA#$index", $experience->company_name);
                $template->setValue("deA#$index", $experience->start_date);
                $template->setValue("aA#$index", $experience->end_date);

            }
        } else {
            $template->setValue("actividadId", '');
            $template->setValue("orgA", '');
            $template->setValue("deA", '');
            $template->setValue("aA", '');
        }

        if ($engineering_design->isNotEmpty()) {
            $template->cloneRow('experienciaId', $engineering_design->count());
        
            foreach ($engineering_design->values() as $i => $design) {
                $index = $i + 1;
                $template->setValue("experienciaId#$index", $design->institution);
                $template->setValue("periE#$index", $design->period);
                $template->setValue("nivelE#$index", $design->level_experience);

            }
        } else {
            $template->setValue("experienciaId", '');
            $template->setValue("periE", '');
            $template->setValue("nivelE", '');
        }

        
        if ($professional_achievement->isNotEmpty()) {

            //Estrcutura original ${logroId} con el nombre de ${nombreL} con una relevancia de ${relevanciaL} creado por:  ${autoresL} en ${lugarL}
            $template->cloneRow('descriptionId', $professional_achievement->count());
        
            foreach ($professional_achievement->values() as $i => $achievement) {
                $index = $i + 1;
                $template->setValue("descriptionId#$index", $achievement->description);

            }
        } else {
            $template->setValue("descriptionId", '');
        }

        if ($participation->isNotEmpty()) {
            $template->cloneRow('participacionId', $participation->count());
        
            foreach ($participation->values() as $i => $participation) {
                $index = $i + 1;
                $template->setValue("participacionId#$index", $participation->institution);
                $template->setValue("periP#$index", $participation->period);
                $template->setValue("nivelP#$index", $participation->level_participation);

            }
        } else {
            $template->setValue("participacionId", '');
            $template->setValue("periP", '');
            $template->setValue("nivelP", '');
        }
        

        if ($award->isNotEmpty()) {
            $template->cloneRow('premioId', $award->count());
        
            foreach ($award->values() as $i => $award) {
                $index = $i + 1;
                $template->setValue("premioId#$index", $award->description);
                //$template->setValue("fechaP#$index", $award->obtained_year);

            }
        } else {
            $template->setValue("premioId", '');
            //$template->setValue("fechaP", '');
        }
        if ($contribution_to_pe) {
            $template->setValue('aportacionId', $contribution_to_pe->description);
        } else {
            $template->setValue("aportacionId", '');
        }
        

        // Guardar el documento generado temporalmente
        $filename = 'CV_' . $cv->cv_id . '.docx';
        $outputPath = storage_path('app/public/' . $filename);
        $template->saveAs($outputPath);

        // Descargar el archivo
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}
