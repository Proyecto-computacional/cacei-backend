<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Evidence;
use App\Models\Reviser;
use App\Models\Notification;


class GenerateNotifications extends Command
{
    protected $signature = 'notifications:generate';
    protected $description = 'Genera notificaciones automáticas 3 días antes del Due_date';

    public function handle()
    {
        //PRUEBA 
        // Simula la fecha de hoy como 2025-06-30
        // $hoy = Carbon::create(2025, 6, 30)->format('Y-m-d'); 

        // Obtener evidencias con fecha de vencimiento igual a la fecha simulada
        //$evidencias = Evidence::where('due_date', $hoy)->get();

        $hoy = Carbon::now();
        $tresDiasAntes = $hoy->addDays(3)->format('Y-m-d');

        // Obtener evidencias con fecha de vencimiento en 3 días
        $evidencias = Evidence::where('due_date', $tresDiasAntes)->get();

        foreach ($evidencias as $evidencia) {
            // Buscar un revisor que tenga asignada la evidencia
            $revisor = Reviser::where('evidence_id', $evidencia->evidence_id)->first();

            if (!$revisor) {
                $this->error("No hay revisor asignado para la evidencia ID: {$evidencia->evidence_id}");
                continue; // Saltar esta iteración si no hay revisor
            }
            try {
                Notification::create([
                    'title' => 'Vence evidencia',
                    'evidence_id' => $evidencia->evidence_id,
                    'notification_date' => Carbon::now(),
                    'user_rpe' => $evidencia->user_rpe, // Notificar al usuario responsable
                    'reviser_id' => $revisor->reviser_id,
                    'description' => 'vence en 3 días.',
                    'seen' => false,
                    'pinned' => false
                ]);
            } catch (\Exception $e) {
                $this->error('Error al crear la notificación: ' . $e->getMessage());
            }
        }

        $this->info('Notificaciones generadas correctamente.');
    }
}