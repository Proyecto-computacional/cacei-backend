<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
class PruebaController extends Controller
{
    //
    public function index(){
        return view('prueba_valida_usuario_v1');
    }
    public function guardar(Request $request): RedirectResponse
    {
         
            // Validar los datos recibidos
        $request->validate([
            'user_rpe' => 'required|string|max:255',
            'usr_mail' => 'required|email|max:255',
            'role' => 'required|string|max:255',
        ]);

        // Guardar en la base de datos (actualizar si ya existe)
        User::updateOrCreate(
            ['user_rpe' => $request->user_rpe],
            [
                'usr_mail' => $request->usr_mail,
                'role' => $request->role,
            ]
        );

        return redirect(RouteServiceProvider::HOME);
    }

}
