<?php
/**
 * Script de diagnóstico para verificar el flujo completo de login
 */

require_once __DIR__ . '/estructura/config/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Diagnóstico de Login Completo</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo ".warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo ".error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }";
echo "table { width: 100%; border-collapse: collapse; margin: 15px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }";
echo "th { background-color: #2c5aa0; color: white; }";
echo ".url-test { word-break: break-all; font-family: monospace; font-size: 12px; }";
echo ".test-form { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 15px 0; }";
echo "input[type='text'], input[type='password'] { width: 200px; padding: 8px; margin: 5px; }";
echo "button { background-color: #2c5aa0; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }";
echo "button:hover { background-color: #1e3d6f; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 Diagnóstico Completo del Sistema de Login</h1>";

// Información de URLs actuales
echo "<h2>📊 URLs del Sistema</h2>";
echo "<table>";
echo "<tr><th>Constante</th><th>Valor</th></tr>";
echo "<tr><td>BASE_URL</td><td class='url-test'>" . BASE_URL . "</td></tr>";
echo "<tr><td>CSS_PATH</td><td class='url-test'>" . CSS_PATH . "</td></tr>";
echo "<tr><td>JS_PATH</td><td class='url-test'>" . JS_PATH . "</td></tr>";
echo "</table>";

// Verificar archivos críticos
echo "<h2>📁 Verificación de Archivos Críticos</h2>";
$archivos_criticos = [
    'Página de login' => BASE_URL . '/estructura/views/inicio.php',
    'JavaScript login' => JS_PATH . '/Inicio-Register.js',
    'Procesador login' => BASE_URL . '/estructura/controllers/procesar_inicio.php',
    'Página después de login' => BASE_URL . '/estructura/views/pag_inicio.php',
    'UsuarioController' => BASE_URL . '/estructura/controllers/UsuarioController.php',
    'AuthService' => BASE_URL . '/estructura/services/AuthService.php'
];

echo "<table>";
echo "<tr><th>Archivo</th><th>URL</th><th>Acción</th></tr>";
foreach ($archivos_criticos as $nombre => $url) {
    echo "<tr>";
    echo "<td>$nombre</td>";
    echo "<td class='url-test'>$url</td>";
    echo "<td><a href='$url' target='_blank' style='color: #2c5aa0;'>🔗 Verificar</a></td>";
    echo "</tr>";
}
echo "</table>";

// Mostrar contenido del JavaScript actual
echo "<h2>📜 Contenido Actual del JavaScript</h2>";
$js_file = __DIR__ . '/estructura/views/js/Inicio-Register.js';
if (file_exists($js_file)) {
    echo "<div class='test-form'>";
    echo "<h4>Archivo: Inicio-Register.js</h4>";
    echo "<pre style='background-color: #f1f3f4; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px;'>";
    echo htmlspecialchars(file_get_contents($js_file));
    echo "</pre>";
    echo "</div>";
} else {
    echo "<div class='error'>❌ El archivo JavaScript no existe: $js_file</div>";
}

// Formulario de prueba de login
echo "<h2>🧪 Prueba de Login en Tiempo Real</h2>";
echo "<div class='test-form'>";
echo "<h4>Formulario de Prueba (igual al real)</h4>";
echo "<form id='testLoginForm'>";
echo "<div>";
echo "<label>Usuario: </label>";
echo "<input type='text' id='testUsername' name='username' placeholder='Nombre de usuario' required>";
echo "</div>";
echo "<div>";
echo "<label>Contraseña: </label>";
echo "<input type='password' id='testPassword' name='password' placeholder='Contraseña' required>";
echo "</div>";
echo "<div style='margin-top: 15px;'>";
echo "<button type='submit'>Probar Login</button>";
echo "</div>";
echo "</form>";

echo "<div id='testResult' style='margin-top: 15px;'></div>";
echo "</div>";

// JavaScript para el formulario de prueba
echo "<script>";
echo "document.getElementById('testLoginForm').addEventListener('submit', function(e) {";
echo "    e.preventDefault();";
echo "    const username = document.getElementById('testUsername').value;";
echo "    const password = document.getElementById('testPassword').value;";
echo "    const resultDiv = document.getElementById('testResult');";
echo "    ";
echo "    resultDiv.innerHTML = '<div style=\"padding: 10px; background-color: #e3f2fd; border-radius: 5px;\">⏳ Procesando login...</div>';";
echo "    ";
echo "    const formData = new FormData();";
echo "    formData.append('username', username);";
echo "    formData.append('password', password);";
echo "    ";
echo "    // URL completa para diagnóstico";
echo "    const loginUrl = '" . BASE_URL . "/estructura/controllers/procesar_inicio.php';";
echo "    console.log('URL de login:', loginUrl);";
echo "    ";
echo "    fetch(loginUrl, {";
echo "        method: 'POST',";
echo "        body: formData";
echo "    })";
echo "    .then(response => {";
echo "        console.log('Respuesta del servidor:', response);";
echo "        if (!response.ok) throw new Error('Error en la respuesta del servidor');";
echo "        return response.json();";
echo "    })";
echo "    .then(data => {";
echo "        console.log('Datos recibidos:', data);";
echo "        let resultHtml = '<div style=\"padding: 15px; border-radius: 5px;';";
echo "        if (data.status === 'success') {";
echo "            resultHtml += ' background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724;\">';";
echo "            resultHtml += '✅ <strong>LOGIN EXITOSO</strong><br>';";
echo "            resultHtml += 'Mensaje: ' + data.message + '<br>';";
echo "            resultHtml += '<br><strong>¿Dónde redirige?</strong><br>';";
echo "            resultHtml += 'URL de redirección que se usaría: <code>pag_inicio.php</code><br>';";
echo "            resultHtml += 'URL completa: <code>" . BASE_URL . "/estructura/views/pag_inicio.php</code><br>';";
echo "            resultHtml += '<br><a href=\"" . BASE_URL . "/estructura/views/pag_inicio.php\" target=\"_blank\" style=\"color: #2c5aa0;\">🔗 Probar acceso a página de inicio</a>';";
echo "        } else {";
echo "            resultHtml += ' background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;\">';";
echo "            resultHtml += '❌ <strong>ERROR DE LOGIN</strong><br>';";
echo "            resultHtml += 'Mensaje: ' + data.message;";
echo "        }";
echo "        resultHtml += '</div>';";
echo "        resultDiv.innerHTML = resultHtml;";
echo "    })";
echo "    .catch(error => {";
echo "        console.error('Error:', error);";
echo "        resultDiv.innerHTML = '<div style=\"padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px;\">❌ <strong>ERROR:</strong> ' + error.message + '</div>';";
echo "    });";
echo "});";
echo "</script>";

// Información adicional
echo "<h2>📋 Checklist de Verificación</h2>";
echo "<div class='warning'>";
echo "<h4>Pasos para verificar el problema:</h4>";
echo "<ol>";
echo "<li>✅ <strong>Usar el formulario de arriba</strong> para probar el login</li>";
echo "<li>✅ <strong>Verificar en la consola del navegador</strong> qué URL se está usando</li>";
echo "<li>✅ <strong>Comprobar la respuesta del servidor</strong> para ver si hay errores</li>";
echo "<li>✅ <strong>Verificar que la página de destino</strong> (pag_inicio.php) existe y funciona</li>";
echo "<li>✅ <strong>Limpiar el cache del navegador</strong> para asegurar que se use el JS actualizado</li>";
echo "</ol>";
echo "</div>";

echo "<div class='success'>";
echo "<h4>URLs que deberían funcionar (con dprado):</h4>";
echo "<ul>";
echo "<li>Login: <a href='" . BASE_URL . "/estructura/views/inicio.php' target='_blank'>" . BASE_URL . "/estructura/views/inicio.php</a></li>";
echo "<li>Después del login: <a href='" . BASE_URL . "/estructura/views/pag_inicio.php' target='_blank'>" . BASE_URL . "/estructura/views/pag_inicio.php</a></li>";
echo "<li>Reservas: <a href='" . BASE_URL . "/estructura/views/reservas.php' target='_blank'>" . BASE_URL . "/estructura/views/reservas.php</a></li>";
echo "</ul>";
echo "</div>";

echo "<div style='margin-top: 30px; padding: 15px; background-color: #e9ecef; border-radius: 5px;'>";
echo "<h4>💡 Notas Importantes</h4>";
echo "<ul>";
echo "<li>Si el login funciona pero redirige mal, el problema está en el JavaScript</li>";
echo "<li>Si el login falla, el problema está en el procesamiento del servidor</li>";
echo "<li>Si aparece 'rpedraza' en alguna URL, significa que hay cache o código estático</li>";
echo "<li>Usa las herramientas de desarrollador (F12) para ver errores en consola</li>";
echo "</ul>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?>
