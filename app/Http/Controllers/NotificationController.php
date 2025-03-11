<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    // Método para obtener todas las notificaciones ordenadas por 'pinned' y luego por 'created_at'
    public function index()
    {
        return Notification::orderBy('pinned', 'desc')  // Ordena primero por 'pinned' en orden descendente
            ->orderBy('created_at', 'desc')  // Luego ordena por 'created_at' en orden descendente
            ->get();  // Devuelve todas las notificaciones
    }

    // Método para alternar el estado de favorito de una notificación específica
    public function toggleFavorite($id)
    {
        $notification = Notification::findOrFail($id);  // Busca la notificación por su ID o lanza error si no se encuentra
        $notification->favorite = !$notification->favorite;  // Cambia el valor del campo 'favorite' (true/false)
        $notification->save();  // Guarda el cambio en la base de datos

        return response()->json(['message' => 'Estado de favorito actualizado']);  // Devuelve una respuesta JSON indicando que se actualizó el estado de favorito
    }

    // Método para alternar el estado de fijado de una notificación específica
    public function togglePinned($id)
    {
        $notification = Notification::findOrFail($id);  // Busca la notificación por su ID o lanza error si no se encuentra
        $notification->pinned = !$notification->pinned;  // Cambia el valor del campo 'pinned' (true/false)
        $notification->save();  // Guarda el cambio en la base de datos

        return response()->json(['message' => 'Estado de fijado actualizado']);  // Devuelve una respuesta JSON indicando que se actualizó el estado de fijado
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
            'Title' => 'required|string|max:20',
            'Evidence_id' => 'required|exists:Evidence,Evidence_id',
            'Notification_date' => 'required|date',
            'User_rpe' => 'required|exists:User,User_rpe',
            'Description' => 'string|max:20',
            'Seen' => 'required|boolean',
        ]);

        return response()->json(['message' => 'Se envió la notificación',]);
    }
}
