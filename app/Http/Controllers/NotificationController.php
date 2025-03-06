<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{ 
    /*
    public function index()
    {
        $user = auth()->user();//Obtiene al usuario actualmente autenticado
        $notifications = $user->notifications;//Accede a la relación notifications definida en User.php y obtiene todas sus notificaciones.(MODIFICAR USER.PHP)

        return response()->json($notifications);//Regresa las notificaciones en formato JSON para que el frontend las pueda usar.
    }*/

    public function markAsSeen($id)
    {
    //Busca una notificación con:
    //Notification_id = $id (que viene en la URL).
    //User_rpe = RPE del usuario autenticado.
        $notification = Notification::where('Notification_id', $id)
            ->where('User_rpe', auth()->user()->user_rpe)
            ->firstOrFail();
        //Marca la notificación como vista (Seen = true) y la guarda
        $notification->Seen = true;
        $notification->save();

        //Devuelve una respuesta JSON de confirmación.
        return response()->json(['message' => 'Notificación marcada como vista']);
    }
}
