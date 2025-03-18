<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Evidence;

class GenerateNotifications extends Command
{
    protected $signature = 'notifications:generate';
    protected $description = 'Genera notificaciones automáticas 3 días antes del Due_date';

    public function handle()
    {
        $hoy = Carbon::now(); 
        $tresDiasAntes = $hoy->addDays(3)->format('Y-m-d');

        // Obtener evidencias con fecha de vencimiento en 3 días
        $evidencias = Evidence::where('due_date', $tresDiasAntes)->get();

        foreach ($evidencias as $evidencia) {
            Notification::create([
                'title' => 'Vence evidencia',
                'evidence_id' => $evidencia->evidence_id,
                'notification_date' => Carbon::now(),
                'user_rpe' => $evidencia->user_rpe, // Notificar al usuario responsable
                'description' => 'vence en 3 días.',
                'seen' => false
            ]);
        }

        $this->info('Notificaciones generadas correctamente.');
    }
}