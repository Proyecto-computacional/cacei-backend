<?php

// app/Http/Controllers/RolePermissionController.php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions' => function ($query) {
            $query->select('permissions.permission_id', 'permission_name');
        }])->get();

        $resultado = $roles->map(function ($rol) {
            return [
                'role_id' => $rol->role_id,
                'role_name' => $rol->role_name,
                'permissions' => $rol->permissions->map(function ($permiso) {
                    return [
                        'permission_id' => $permiso->permission_id,
                        'permission_name' => $permiso->permission_name,
                        'is_enabled' => (bool) $permiso->pivot->is_enabled,
                    ];
                }),
            ];
        });

        return response()->json($resultado);
    }

    public function updateEnable(Request $request, $role_id, $permission_id)
    {
        $validated = $request->validate([
            'is_enabled' => 'required|boolean',
        ]);

        RolePermission::where('role_id', $role_id)
            ->where('permission_id', $permission_id)
            ->update(['is_enabled' => $validated['is_enabled']]);

        return response()->json(['message' => 'Permiso actualizado correctamente']);
    }
}
