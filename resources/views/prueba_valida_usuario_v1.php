<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba API REST - POST</title>

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
                // Decodificar la respuesta JSON
                $data = json_decode($response, true); // Convertir en array asociativo

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Verificar si la respuesta es correcta
                    if (!empty($data['correcto']) && $data['correcto'] === true && !empty($data['datos'])) {
                        // Obtener el primer elemento del array "datos"
                         $usuario = $data['datos'][0];

                        // Extraer valores específicos
                        $rpe = $usuario['rpe'] ?? 'No disponible';
                        $correo = $usuario['correo'] ?? 'No disponible';
                        $cargo = $usuario['cargo'] ?? 'No disponible';

                        // Mostrar los datos extraídos
                        echo "<h3>Datos del usuario:</h3>";
                        echo "<ul>";
                        echo "<li><strong>RPE:</strong> " . htmlspecialchars($rpe, ENT_QUOTES, 'UTF-8') . "</li>";
                        echo "<li><strong>Correo:</strong> " . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . "</li>";
                        echo "<li><strong>Cargo:</strong> " . htmlspecialchars($cargo, ENT_QUOTES, 'UTF-8') . "</li>";
                        echo "</ul>";
                        // **Formulario oculto para enviar los datos al controlador de Laravel**
            echo '
            <form method="POST" action="/guardar">
                <input type="hidden" name="user_rpe" value="' . htmlspecialchars($rpe, ENT_QUOTES, 'UTF-8') . '">
                <input type="hidden" name="user_mail" value="' . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . '">
                <input type="hidden" name="user_role" value="' . htmlspecialchars($cargo, ENT_QUOTES, 'UTF-8') . '">
                <button type="submit">Guardar en la Base de Datos</button>
            </form>';
                    } else {
                        echo "<p>No se encontraron datos válidos.</p>";
                    }
                } else {
                     echo "<p>Error al decodificar JSON.</p>";
                }
            }

            // Cerrar la conexión cURL
            curl_close($ch);
        }
        ?>
    </pre>
</body>
</html>
