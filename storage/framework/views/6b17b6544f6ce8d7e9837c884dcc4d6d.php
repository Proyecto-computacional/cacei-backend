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
        <form method="GET" action="<?php echo e(route('usuarios.index')); ?>" class="mb-3">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por RPE..." value="<?php echo e(request('buscar')); ?>">
            <button type="submit" class="btn btn-primary mt-2">Buscar</button>
        </form>       
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
                <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($usuario->user_rpe); ?></td>
                    <td>
                        <select class="form-select rol-select" data-user-id="<?php echo e($usuario->user_rpe); ?>">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($rol); ?>" <?php echo e($usuario->user_role == $rol ? 'selected' : ''); ?>>
                                <?php echo e($rol); ?>

                                </option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td>
                        <i class="<?php echo e(iluminarPermiso($usuario->user_role, 'ver')); ?>">Ver</i>
                        <i class="<?php echo e(iluminarPermiso($usuario->user_role, 'crear')); ?>">Crear</i>
                        <i class="<?php echo e(iluminarPermiso($usuario->user_role, 'eliminar')); ?>">Eliminar</i>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                    }
                });

                $.ajax({
                    url: "<?php echo e(route('usuarios.actualizarRol')); ?>",
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
<?php /**PATH C:\Users\redar\laragon\www\proyectoCACEI\resources\views/usuarios/index.blade.php ENDPATH**/ ?>