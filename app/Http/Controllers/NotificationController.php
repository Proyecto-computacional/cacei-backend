<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    // Método para obtener todas las notificaciones ordenadas por 'pinned' y luego por 'created_at'
    public function index(Request $request)
    {
        $notifications = Notification::where('user_rpe', $request->user_rpe)
            ->orderBy('notification_date', 'desc')
            ->get();

        // Añade información relevante
        $payload = $notifications->map(function ($n) {

            return [
                'notification_id' => $n->notification_id,
                'title' => $n->title,
                'description' => $n->description,
                'seen' => (bool) $n->seen,
                'pinned' => (bool) $n->pinned,
                'starred' => (bool) $n->starred,
                'notification_date' => $n->notification_date,

                'evidence' => $n->evidence ? [
                    'standard' => $n->evidence->standard ? [
                        'standard_name' => $n->evidence->standard->standard_name,
                    ] : null,
                ] : null,

                'reviser' => $n->reviser ? [
                    'user_rpe' => $n->reviser->user_rpe,
                    'user_name' => $n->reviser->user_name,
                    'user_mail' => $n->reviser->user_mail,
                ] : null,
            ];
        });

        return response()->json($payload);
    }

    // Método para alternar y guardar el estado de favorito de una notificación específica
    public function toggleFavorite(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);
        $notification->starred = !$notification->starred;
        $notification->save();

        return response()->json(['message' => 'Estado de favorito actualizado']);
    }

    // Método para alternar y guardar el estado de fijado de una notificación específica
    public function togglePinned(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);
        $notification->pinned = !$notification->pinned;
        $notification->save();

        return response()->json(['message' => 'Estado de fijado actualizado']);
    }

    // Método para hacer "soft delete" a una notificación por su ID
    public function toggleDeleted(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);
        $notification->seen = !$notification->seen;  // Cambia el valor del campo 'seen' (true/false)
        $notification->save();

        return response()->json(['message' => 'Notificación eliminada']);
    }

    // Método para eliminar (hard delete) una notificación por su ID
    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json(['message' => 'Notificación eliminada']);
    }

    // Método para enviar una nueva notificación
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:20',
            'evidence_id' => 'nullable|integer',
            'notification_date' => 'required|date',
            'user_rpe' => 'required|string',
            'reviser_id' => 'required|integer',
            'description' => 'string|max:20',
        ]);

        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (Notification::where('notification_id', $randomId)->exists()); // Verifica que no se repita

        $notification = Notification::create([
            'title' => $request->title,
            'evidence_id' => $request->evidence_id,
            'notification_date' => $request->notification_date,
            'user_rpe' => $request->user_rpe,
            'reviser_id' => $request->reviser_id,
            'description' => $request->description,
            'seen' => false,
            'pinned' => false,
        ]);

        // Retornar la respuesta con la notificación creada
        return response()->json([
            'message' => 'Notificación guardada exitosamente',
            'notification' => $notification
        ], 201);
    }
}
