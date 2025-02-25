<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class PruebaController extends Controller
{
    //
    public function index(){
        return view('prueba_valida_usuario_v1');
    }
    public function guardar(Request $request)
    {
        // Validar que los datos sean correctos
        $request->validate([
            'rpe' => 'required|integer',  // Aseguramos que rpe sea un número entero
            'cargo' => 'required|string',
        ]);

        // Guardar los datos en la base de datos
        $user = new User();
        $user->user_rpe = $request->rpe;  // Convertimos rpe a entero
        $user->role = $request->cargo;   
        $user->save();          

        return redirect()->back()->with('success', 'Usuario registrado correctamente.');
    }

}
