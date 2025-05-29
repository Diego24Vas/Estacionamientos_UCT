<?php
/**
 * Simulador de login para identificar el problema exacto
 */

require_once __DIR__ . '/estructura/config/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Simulador de Login - Diagnóstico</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo ".test-box { background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 5px; border: 1px solid #ddd; }";
echo ".success { background: #d4edda; border-color: #c3e6cb; color: #155724; }";
echo ".error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }";
echo ".code { background: #f1f3f4; padding: 10px; border-radius: 3px; font-family: monospace; margin: 10px 0; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔬 Simulador de Login - Paso a Paso</h1>";

// Verificar información del servidor actual
echo "<div class='test-box'>";
echo "<h3>📊 Información del Servidor Actual</h3>";
echo "<strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No disponible') . "<br>";
echo "<strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'No disponible') . "<br>";
echo "<strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "<br>";
echo "<strong>BASE_URL detectado:</strong> " . BASE_URL . "<br>";
echo "</div>";

// Simular exactamente lo que hace el JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    echo "<div class='test-box'>";
    echo "<h3>🧪 Resultado de la Simulación</h3>";
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<strong>Usuario ingresado:</strong> " . htmlspecialchars($username) . "<br>";
    echo "<strong>Contraseña ingresada:</strong> " . str_repeat('*', strlen($password)) . "<br><br>";
    
    // Simular la llamada al controlador
    echo "<strong>URL que usaría el fetch:</strong><br>";
    echo "<div class='code'>" . BASE_URL . "/estructura/controllers/procesar_inicio.php</div>";
    
    // Realizar la simulación real
    try {
        // Configurar datos para enviar
        $data = http_build_query([
            'username' => $username,
            'password' => $password
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
            ]
        ]);
        
        $url = BASE_URL . "/estructura/controllers/procesar_inicio.php";
        echo "<strong>Enviando petición a:</strong> $url<br><br>";
        
        $response = file_get_contents($url, false, $context);
        
        if ($response !== false) {
            echo "<div class='success'>";
            echo "<strong>✅ Respuesta del servidor:</strong><br>";
            echo "<div class='code'>" . htmlspecialchars($response) . "</div>";
            
            // Intentar decodificar como JSON
            $json_response = json_decode($response, true);
            if ($json_response !== null) {
                echo "<strong>Datos decodificados:</strong><br>";
                echo "<div class='code'>";
                echo "Status: " . ($json_response['status'] ?? 'No definido') . "<br>";
                echo "Message: " . ($json_response['message'] ?? 'No definido') . "<br>";
                echo "</div>";
                
                if ($json_response['status'] === 'success') {
                    echo "<strong>🎯 URL de redirección que usaría JavaScript:</strong><br>";
                    echo "<div class='code'>window.location.href = 'pag_inicio.php';</div>";
                    echo "<strong>URL completa resultante:</strong><br>";
                    echo "<div class='code'>" . BASE_URL . "/estructura/views/pag_inicio.php</div>";
                    
                    // Verificar si la página de destino existe
                    $destino_url = BASE_URL . "/estructura/views/pag_inicio.php";
                    echo "<br><a href='$destino_url' target='_blank' style='background: #2c5aa0; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>🔗 Probar URL de destino</a>";
                }
            }
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "❌ <strong>Error:</strong> No se pudo conectar al servidor de login";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "❌ <strong>Error en la simulación:</strong> " . $e->getMessage();
        echo "</div>";
    }
    
    echo "</div>";
}

// Formulario de prueba
echo "<div class='test-box'>";
echo "<h3>📝 Formulario de Simulación</h3>";
echo "<form method='POST'>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Usuario: </label>";
echo "<input type='text' name='username' required style='padding: 8px; width: 200px;'>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Contraseña: </label>";
echo "<input type='password' name='password' required style='padding: 8px; width: 200px;'>";
echo "</div>";
echo "<div style='margin: 15px 0;'>";
echo "<button type='submit' name='test_login' style='background: #2c5aa0; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Simular Login</button>";
echo "</div>";
echo "</form>";
echo "</div>";

// Información adicional para debugging
echo "<div class='test-box'>";
echo "<h3>🔍 Debugging Information</h3>";
echo "<strong>Archivos a verificar:</strong><br>";
echo "1. <a href='" . JS_PATH . "/Inicio-Register.js' target='_blank'>JavaScript actual</a><br>";
echo "2. <a href='" . BASE_URL . "/estructura/controllers/procesar_inicio.php' target='_blank'>Controlador de login</a><br>";
echo "3. <a href='" . BASE_URL . "/estructura/views/pag_inicio.php' target='_blank'>Página de destino</a><br><br>";

echo "<strong>Pasos para resolver el problema:</strong><br>";
echo "1. Usar el formulario de arriba para simular el login<br>";
echo "2. Verificar que la respuesta sea correcta<br>";
echo "3. Comprobar que la URL de destino no contenga 'rpedraza'<br>";
echo "4. Si aparece 'rpedraza', hay que revisar el cache del navegador<br>";
echo "</div>";

echo "</body>";
echo "</html>";
?>
