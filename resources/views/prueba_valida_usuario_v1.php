<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba API REST - POST</title>

    <script>
        function enableSaveButton(rpe, cargo) {
            // Habilitar botón de guardar y almacenar los valores en campos ocultos
            document.getElementById('saveButton').disabled = false;
            document.getElementById('rpe').value = rpe;
            document.getElementById('cargo').value = cargo;
        }
    </script>
</head>
<body>
    <h1>Prueba de API REST - Método POST - Valida Usuarios</h1>
    
    <form method="POST" action="/prueba">
        <label for="endpoint">URL Servicio:</label>
        <input type="text" id="endpoint" name="endpoint" size="80" value="https://servicios.ing.uaslp.mx/ws_cacei/ValidaUsuario.php" required>
        <br><br>
        
        <label for="payload">Parámetros Datos (JSON):</label>
        <input type="text" id="payload" name="payload" size="80" value='{"key":"B3E06D96-1562-4713-BCD7-7F762A87F205","rpe":"10285","contra":"Cacei#FI@2025"}' required>
        <br><br>
        
        <button type="submit">Enviar Solicitud</button>
    </form>

    <h2>Resultado:</h2>

    <pre>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $endpoint = $_POST['endpoint'];
            $payload = $_POST['payload'];

            
            // Inicializar cURL
            $ch = curl_init($endpoint);
            

            // Configurar cURL para el método POST
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);

            // Deshabilitar la verificación del certificado SSL - Solo si se prueba en modo local
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            // Ejecutar la solicitud
            $response = curl_exec($ch);
            
            // Verificar errores
            if (curl_errno($ch)) {
                echo "Error en la solicitud: " . curl_error($ch);
            } else {
                // Mostrar la respuesta de la API
                echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
            }

            // Cerrar la conexión cURL
            curl_close($ch);
        }
        ?>
    </pre>
    <form method="POST" action="/guardar">
        @csrf
        <input type="hidden" id="rpe" name="rpe">
        <input type="hidden" id="cargo" name="cargo">
        <button type="submit" id="saveButton" disabled>Guardar Usuario</button>
    </form>
</body>
</html>
