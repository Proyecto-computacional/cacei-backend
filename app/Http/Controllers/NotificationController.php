<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    // Método para obtener todas las notificaciones ordenadas por 'pinned' y luego por 'created_at'
    public function index(Request $request)
    {
        $notifications = Notification::where('user_rpe', $request->user_rpe)  // Ordena primero por 'pinned' en orden descendente
            ->orderBy('notification_date', 'desc')  // Luego ordena por 'created_at' en orden descendente
            ->get();  // Devuelve todas las notificaciones
        return response()->json($notifications);
    }

    // Método para alternar el estado de favorito de una notificación específica
    public function toggleFavorite(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);  // Busca la notificación por su ID o lanza error si no se encuentra
        $notification->starred = !$notification->starred;  // Cambia el valor del campo 'favorite' (true/false)
        $notification->save();  // Guarda el cambio en la base de datos

        return response()->json(['message' => 'Estado de favorito actualizado']);  // Devuelve una respuesta JSON indicando que se actualizó el estado de favorito
    }

    // Método para alternar el estado de fijado de una notificación específica
    public function togglePinned(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);  // Busca la notificación por su ID o lanza error si no se encuentra
        $notification->pinned = !$notification->pinned;  // Cambia el valor del campo 'pinned' (true/false)
        $notification->save();  // Guarda el cambio en la base de datos

        return response()->json(['message' => 'Estado de fijado actualizado']);  // Devuelve una respuesta JSON indicando que se actualizó el estado de fijado
    }

    // Método para hacer soft delete a una notificación por su ID
    public function toggleDeleted(Request $request)
    {
        $notification = Notification::findOrFail($request->notification_id);  // Busca la notificación por su ID o lanza error si no se encuentra
        $notification->seen = !$notification->seen;  // Cambia el valor del campo 'seen' (true/false)
        $notification->save();  // Guarda el cambio en la base de datos

        return response()->json(['message' => 'Notificación eliminada']);  // Devuelve una respuesta JSON indicando que se actualizó el estado de fijado
    }

    // Método para eliminar una notificación por su ID
    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();  // Busca y elimina la notificación por su ID
        return response()->json(['message' => 'Notificación eliminada']);  // Devuelve una respuesta JSON indicando que la notificación fue eliminada
    }

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
            'notification_id' => $randomId,
            'title' => $request->title,
            'evidence_id' => $request->evidence_id,
            'notification_date' => $request->notification_date,
            'user_rpe' => $request->user_rpe,
            //'reviser_id' => $request->reviser_id,
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
