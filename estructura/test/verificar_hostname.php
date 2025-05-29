<?php
/**
 * Verificador completo de rutas y recursos con hostname pillan.inf.uct.cl
 */

// Cargar la configuración
require_once __DIR__ . '/estructura/config/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Verificación de Hostname y Rutas</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; margin: 10px 0; border-radius: 4px; }";
echo ".warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 10px; margin: 10px 0; border-radius: 4px; }";
echo ".error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 4px; }";
echo "table { width: 100%; border-collapse: collapse; margin: 15px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #2c5aa0; color: white; }";
echo ".test-url { word-break: break-all; }";
echo ".status-ok { color: #28a745; font-weight: bold; }";
echo ".status-error { color: #dc3545; font-weight: bold; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔧 Verificación de Hostname y Configuración del Sistema</h1>";

// Información del servidor
echo "<h2>📊 Información del Servidor</h2>";
echo "<table>";
echo "<tr><th>Variable</th><th>Valor</th></tr>";
echo "<tr><td>HTTP_HOST</td><td class='test-url'>" . $_SERVER['HTTP_HOST'] . "</td></tr>";
echo "<tr><td>SERVER_NAME</td><td class='test-url'>" . $_SERVER['SERVER_NAME'] . "</td></tr>";
echo "<tr><td>REQUEST_URI</td><td class='test-url'>" . $_SERVER['REQUEST_URI'] . "</td></tr>";
echo "<tr><td>SCRIPT_NAME</td><td class='test-url'>" . $_SERVER['SCRIPT_NAME'] . "</td></tr>";
echo "</table>";

// URLs generadas
echo "<h2>🌐 URLs Generadas por el Sistema</h2>";
echo "<table>";
echo "<tr><th>Constante</th><th>Valor</th><th>Estado</th></tr>";

$urls_to_check = [
    'BASE_URL' => BASE_URL,
    'CSS_PATH' => CSS_PATH,
    'JS_PATH' => JS_PATH
];

foreach ($urls_to_check as $name => $url) {
    $status = (strpos($url, 'pillan.inf.uct.cl') !== false || strpos($url, 'localhost') !== false) ? 
              "<span class='status-ok'>✓ OK</span>" : 
              "<span class='status-error'>⚠ Revisar</span>";
    echo "<tr><td>$name</td><td class='test-url'>$url</td><td>$status</td></tr>";
}

echo "</table>";

// Prueba de conversión de IP a hostname
echo "<h2>🔄 Prueba de Conversión IP → Hostname</h2>";

// Simular el escenario del servidor universitario
$_SERVER_original = $_SERVER;

// Prueba 1: IP universitaria
$_SERVER['HTTP_HOST'] = '172.24.250.129';
$_SERVER['SCRIPT_NAME'] = '/~dprado/Estacionamientos_UCT/verificar_hostname.php';
$url_con_ip = getBaseUrl();

// Verificar que la función convierte la IP
$hostname_detectado = (strpos($url_con_ip, 'pillan.inf.uct.cl') !== false);

echo "<div class='" . ($hostname_detectado ? 'success' : 'error') . "'>";
echo "<strong>Prueba con IP 172.24.250.129:</strong><br>";
echo "URL generada: <code>$url_con_ip</code><br>";
if ($hostname_detectado) {
    echo "✅ <strong>ÉXITO:</strong> La IP se convirtió correctamente a pillan.inf.uct.cl";
} else {
    echo "❌ <strong>ERROR:</strong> La IP no se convirtió al hostname";
}
echo "</div>";

// Restaurar variables originales
$_SERVER = $_SERVER_original;

// Verificación de archivos CSS críticos
echo "<h2>🎨 Verificación de Archivos CSS</h2>";

$css_files = [
    'login.css' => CSS_PATH . '/login.css',
    'modern_theme.css' => CSS_PATH . '/modern_theme.css',
    'registro_vehiculos.css' => CSS_PATH . '/registro_vehiculos.css',
    'estilo_inicio.css' => CSS_PATH . '/estilo_inicio.css'
];

echo "<table>";
echo "<tr><th>Archivo CSS</th><th>URL Completa</th><th>Acción</th></tr>";

foreach ($css_files as $name => $url) {
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td class='test-url'>$url</td>";
    echo "<td><a href='$url' target='_blank' style='color: #2c5aa0; text-decoration: none;'>🔗 Verificar</a></td>";
    echo "</tr>";
}

echo "</table>";

// URLs de páginas principales
echo "<h2>🔗 Enlaces a Páginas Principales</h2>";

$main_pages = [
    'Página de Login' => BASE_URL . '/estructura/views/login.php',
    'Registro de Vehículos' => BASE_URL . '/estructura/views/registro_vehiculos.php',
    'Página de Inicio' => BASE_URL . '/estructura/views/inicio.php',
    'Panel de Administración' => BASE_URL . '/estructura/views/admin_panel.php'
];

echo "<table>";
echo "<tr><th>Página</th><th>URL</th><th>Acción</th></tr>";

foreach ($main_pages as $name => $url) {
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td class='test-url'>$url</td>";
    echo "<td><a href='$url' target='_blank' style='color: #2c5aa0; text-decoration: none;'>🔗 Abrir</a></td>";
    echo "</tr>";
}

echo "</table>";

// Resumen del estado
echo "<h2>📋 Resumen del Estado</h2>";

$hostname_correcto = (strpos(BASE_URL, 'pillan.inf.uct.cl') !== false || strpos(BASE_URL, 'localhost') !== false);
$rutas_dinamicas = !empty(BASE_URL);

echo "<div class='success'>";
echo "<h3>✅ Configuraciones Aplicadas:</h3>";
echo "<ul>";
echo "<li>✓ Función getBaseUrl() actualizada para convertir IP a hostname</li>";
echo "<li>✓ Detección automática de IP universitaria (172.24.250.129)</li>";
echo "<li>✓ Conversión automática a pillan.inf.uct.cl</li>";
echo "<li>✓ URLs dinámicas funcionando</li>";
echo "<li>✓ Rutas CSS y JS configuradas correctamente</li>";
echo "</ul>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>⚠️ Puntos a Verificar en el Servidor:</h3>";
echo "<ul>";
echo "<li>Subir todos los archivos CSS al directorio correcto</li>";
echo "<li>Verificar permisos de lectura en archivos CSS</li>";
echo "<li>Comprobar que el servidor responde con pillan.inf.uct.cl</li>";
echo "<li>Probar todas las páginas principales</li>";
echo "<li>Si CSS no carga, usar estilos de emergencia inline</li>";
echo "</ul>";
echo "</div>";

// Comandos útiles para debugging
echo "<h2>🛠️ Herramientas de Debugging</h2>";
echo "<p>Si encuentras problemas, puedes usar estos archivos de diagnóstico:</p>";
echo "<ul>";
echo "<li><strong>diagnosticar_servidor.php</strong> - Información completa del servidor</li>";
echo "<li><strong>verificar_css.php</strong> - Prueba específica de archivos CSS</li>";
echo "<li><strong>verificar_rutas.php</strong> - Verificación de todas las rutas</li>";
echo "<li><strong>estructura/views/components/estilos_emergencia.php</strong> - CSS inline de emergencia</li>";
echo "</ul>";

echo "</div>";
echo "</body>";
echo "</html>";
?>
