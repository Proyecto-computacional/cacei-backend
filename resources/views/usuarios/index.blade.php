<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .text-primary {
    color: blue;
}

.text-muted {
    color: gray;
}

        </style>
    </head>
    
    <body class="antialiased">
        @foreach ($usuarios as $usuario)
        {{ $usuario->rpe }}
        @endforeach
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th>RPE</th>
                        <th>Rol</th>
                        <th>Configuración de permisos</th>
                    </tr>
                </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->user_rpe }}</td>
                    <td>
                        <select class="form-select rol-select" data-user-id="{{ $usuario->user_rpe }}">
                            @foreach($roles as $rol)
                                <option value="{{ $rol }}" {{ $usuario->user_role == $rol ? 'selected' : '' }}>
                                {{ $rol }}
                                </option>
                             @endforeach
                        </select>
                    </td>
                    <td>
                        <i class="{{ iluminarPermiso($usuario->user_role, 'ver') }}">Ver</i>
                        <i class="{{ iluminarPermiso($usuario->user_role, 'crear') }}">Crear</i>
                        <i class="{{ iluminarPermiso($usuario->user_role, 'eliminar') }}">Eliminar</i>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".rol-select").change(function() {
                var userId = $(this).data("user-id"); // ID del usuario
                var nuevoRol = $(this).val(); // Nuevo rol seleccionado
                $.ajaxSetup({
                    headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: "{{ route('usuarios.actualizarRol') }}",
                    type: "POST",
                    data: {
                        user_id: userId,
                        rol: nuevoRol
                    },
                    success: function(response) {
                        alert("Rol actualizado correctamente");
                    },
                    error: function(xhr, status, error) {
                        alert("Error al actualizar el rol: " + xhr.responseText);
                    }
                });

            });
        });
    </script>
