<?php
/**
 * Diagnóstico específico para verificar la detección correcta del usuario
 */

// Cargar la configuración
require_once __DIR__ . '/estructura/config/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Diagnóstico de Usuario y Rutas</title>";
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
echo ".highlight { background-color: #ffeb3b; padding: 2px 4px; border-radius: 3px; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 Diagnóstico de Usuario y Detección de Rutas</h1>";

// Información actual del servidor
echo "<h2>📊 Variables del Servidor Actual</h2>";
echo "<table>";
echo "<tr><th>Variable</th><th>Valor</th></tr>";
echo "<tr><td>HTTP_HOST</td><td class='url-test'>" . ($_SERVER['HTTP_HOST'] ?? 'No disponible') . "</td></tr>";
echo "<tr><td>SERVER_NAME</td><td class='url-test'>" . ($_SERVER['SERVER_NAME'] ?? 'No disponible') . "</td></tr>";
echo "<tr><td>REQUEST_URI</td><td class='url-test'>" . ($_SERVER['REQUEST_URI'] ?? 'No disponible') . "</td></tr>";
echo "<tr><td>SCRIPT_NAME</td><td class='url-test'>" . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "</td></tr>";
echo "<tr><td>PHP_SELF</td><td class='url-test'>" . ($_SERVER['PHP_SELF'] ?? 'No disponible') . "</td></tr>";
echo "</table>";

// Análisis de detección de usuario
echo "<h2>👤 Análisis de Detección de Usuario</h2>";

$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$request_uri = $_SERVER['REQUEST_URI'] ?? '';

// Extraer el usuario del script name
$usuario_detectado = '';
if (preg_match('/\/~([^\/]+)\//', $script_name, $matches)) {
    $usuario_detectado = $matches[1];
} elseif (preg_match('/\/~([^\/]+)\//', $request_uri, $matches)) {
    $usuario_detectado = $matches[1];
}

echo "<table>";
echo "<tr><th>Elemento</th><th>Valor</th><th>Estado</th></tr>";
echo "<tr><td>Usuario detectado</td><td class='highlight'>" . ($usuario_detectado ?: 'No detectado') . "</td>";

if ($usuario_detectado === 'dprado') {
    echo "<td class='success' style='border:none; background:none; color:#155724;'>✅ CORRECTO</td>";
} elseif ($usuario_detectado === 'rpedraza') {
    echo "<td class='error' style='border:none; background:none; color:#721c24;'>❌ INCORRECTO (debe ser dprado)</td>";
} else {
    echo "<td class='warning' style='border:none; background:none; color:#856404;'>⚠ NO DETECTADO</td>";
}
echo "</tr>";
echo "</table>";

// URLs generadas por el sistema
echo "<h2>🌐 URLs Generadas por el Sistema</h2>";
echo "<table>";
echo "<tr><th>Constante</th><th>Valor Generado</th><th>Usuario en URL</th><th>Estado</th></tr>";

$urls = [
    'BASE_URL' => BASE_URL,
    'CSS_PATH' => CSS_PATH,
    'JS_PATH' => JS_PATH
];

foreach ($urls as $name => $url) {
    // Extraer usuario de la URL generada
    $usuario_en_url = '';
    if (preg_match('/\/~([^\/]+)\//', $url, $matches)) {
        $usuario_en_url = $matches[1];
    }
    
    $estado = '';
    if ($usuario_en_url === 'dprado') {
        $estado = "<span style='color:#155724;'>✅ CORRECTO</span>";
    } elseif ($usuario_en_url === 'rpedraza') {
        $estado = "<span style='color:#721c24;'>❌ INCORRECTO</span>";
    } elseif (empty($usuario_en_url)) {
        $estado = "<span style='color:#856404;'>⚠ SIN USUARIO</span>";
    } else {
        $estado = "<span style='color:#856404;'>⚠ OTRO: $usuario_en_url</span>";
    }
    
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td class='url-test'>$url</td>";
    echo "<td class='highlight'>" . ($usuario_en_url ?: 'Ninguno') . "</td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}

echo "</table>";

// Prueba de páginas principales
echo "<h2>🔗 Prueba de Enlaces a Páginas Principales</h2>";

$paginas = [
    'Login' => BASE_URL . '/estructura/views/inicio.php',
    'Registro' => BASE_URL . '/estructura/views/registro.php',
    'Reservas' => BASE_URL . '/estructura/views/reservas.php',
    'Panel Admin' => BASE_URL . '/estructura/views/pagina_admin.php',
    'Estadísticas' => BASE_URL . '/estructura/views/estadisticas.php'
];

echo "<table>";
echo "<tr><th>Página</th><th>URL Generada</th><th>Usuario</th><th>Acción</th></tr>";

foreach ($paginas as $nombre => $url) {
    $usuario_en_url = '';
    if (preg_match('/\/~([^\/]+)\//', $url, $matches)) {
        $usuario_en_url = $matches[1];
    }
    
    echo "<tr>";
    echo "<td>$nombre</td>";
    echo "<td class='url-test'>$url</td>";
    echo "<td>" . ($usuario_en_url ?: 'Ninguno') . "</td>";
    echo "<td><a href='$url' target='_blank' style='color: #2c5aa0;'>🔗 Probar</a></td>";
    echo "</tr>";
}

echo "</table>";

// Verificación específica para JavaScript
echo "<h2>📜 Verificación de JavaScript</h2>";

$js_files = [
    'Inicio-Register.js' => JS_PATH . '/Inicio-Register.js'
];

echo "<table>";
echo "<tr><th>Archivo JS</th><th>URL</th><th>Estado</th></tr>";

foreach ($js_files as $name => $url) {
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td class='url-test'>$url</td>";
    echo "<td><a href='$url' target='_blank' style='color: #2c5aa0;'>🔗 Verificar</a></td>";
    echo "</tr>";
}

echo "</table>";

// Resumen y recomendaciones
$tiene_problemas = false;
if (strpos(BASE_URL, '~rpedraza') !== false) {
    $tiene_problemas = true;
}

if ($tiene_problemas) {
    echo "<div class='error'>";
    echo "<h3>❌ Problemas Detectados</h3>";
    echo "<ul>";
    echo "<li>El sistema está generando URLs con el usuario incorrecto (~rpedraza)</li>";
    echo "<li>Esto causará errores 500 al intentar acceder a las páginas</li>";
    echo "</ul>";
    echo "<h4>Posibles causas:</h4>";
    echo "<ul>";
    echo "<li>Variables de servidor incorrectas</li>";
    echo "<li>Configuración de detección de rutas con errores</li>";
    echo "<li>Cache del navegador o servidor</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h3>✅ Sistema Funcionando Correctamente</h3>";
    echo "<ul>";
    echo "<li>Las URLs se están generando con el usuario correcto (dprado)</li>";
    echo "<li>La detección automática está funcionando</li>";
    echo "<li>Las páginas deberían ser accesibles</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div class='warning'>";
echo "<h3>🛠️ Pasos de Verificación Recomendados</h3>";
echo "<ol>";
echo "<li>Probar los enlaces de las páginas principales arriba</li>";
echo "<li>Verificar que el archivo JavaScript se carga correctamente</li>";
echo "<li>Limpiar cache del navegador si es necesario</li>";
echo "<li>Verificar que todos los archivos estén subidos al servidor</li>";
echo "</ol>";
echo "</div>";

echo "<div style='margin-top: 30px; padding: 15px; background-color: #e9ecef; border-radius: 5px;'>";
echo "<h4>📋 Información Adicional</h4>";
echo "<p><strong>Archivo actual:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Hora de ejecución:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?>
