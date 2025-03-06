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
        <form method="GET" action="{{ route('usuarios.index') }}" class="mb-3">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por RPE..." value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-primary mt-2">Buscar</button>
        </form>       
        <div class="container">
            <table id="usuariosTable" class="table">
                <thead>
                    <tr>
                        <th>RPE</th>
                        <th>Rol</th>
                        <th>Configuración de permisos</th>
                    </tr>
                </thead>
            <tbody>
                <!-- Aquí se llenarán los datos con JavaScript-->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $.getJSON("{{ route('usuarios.index') }}", function(data) {
        let usuarios = data.usuarios.data; // Acceder a la paginación
        let tbody = $("#usuariosTable tbody");
        tbody.empty(); // Limpiamos la tabla

        usuarios.forEach(function(usuario) {
            let row = `<tr>
                <td>${usuario.user_rpe}</td>
                <td>${usuario.user_role}</td>
                <td>---</td> <!-- Aquí puedes añadir la asignación si la tienes -->
                <td>
                    <span>${usuario.user_role === 'DIRECTIVO' ? '👀' : ''}</span>
                    <span>${['JEFE DE AREA', 'COORDINADOR DE CARRERA', 'PROFESOR RESPONSABLE'].includes(usuario.user_role) ? '✅' : ''}</span>
                    <span>${['PROFESOR', 'TUTOR ACADEMICO', 'DEPARTAMENTO UNIVERSITARIO', 'PERSONAL DE APOYO'].includes(usuario.user_role) ? '✏️' : ''}</span>
                </td>
            </tr>`;
            tbody.append(row);
        });
    });
});
</script>