<?php
/**
 * Archivo de verificación para comprobar que todas las rutas dinámicas
 * están funcionando correctamente en el sistema de estacionamientos UCT
 */

// Incluir el archivo de configuración
require_once 'estructura/config/config.php';

echo "<!DOCTYPE html>\n";
echo "<html lang='es'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Verificación de Rutas Dinámicas - Estacionamientos UCT</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }\n";
echo "        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }\n";
echo "        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }\n";
echo "        .success { background-color: #d4edda; border-left: 4px solid #28a745; }\n";
echo "        .error { background-color: #f8d7da; border-left: 4px solid #dc3545; }\n";
echo "        .info { background-color: #e2e3e5; border-left: 4px solid #6c757d; }\n";
echo "        .test-link { display: inline-block; margin: 5px 10px; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }\n";
echo "        .test-link:hover { background: #0056b3; }\n";
echo "        code { background: #f1f3f4; padding: 2px 6px; border-radius: 3px; }\n";
echo "        .resource-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin: 20px 0; }\n";
echo "        .resource-card { padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";

echo "<div class='container'>\n";
echo "<h1>🚗 Verificación de Rutas Dinámicas - Estacionamientos UCT</h1>\n";

// Mostrar información del entorno actual
echo "<div class='status info'>\n";
echo "<h3>📍 Información del Entorno Actual</h3>\n";
echo "<p><strong>Host:</strong> <code>" . ($_SERVER['HTTP_HOST'] ?? 'No disponible') . "</code></p>\n";
echo "<p><strong>Script actual:</strong> <code>" . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "</code></p>\n";
echo "<p><strong>Protocolo:</strong> <code>" . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'HTTPS' : 'HTTP') . "</code></p>\n";
echo "<p><strong>Puerto:</strong> <code>" . ($_SERVER['SERVER_PORT'] ?? 'No disponible') . "</code></p>\n";
echo "</div>\n";

// Mostrar las rutas detectadas
echo "<div class='status success'>\n";
echo "<h3>✅ Rutas Detectadas Dinámicamente</h3>\n";
echo "<p><strong>BASE_URL:</strong> <code>" . BASE_URL . "</code></p>\n";
echo "<p><strong>CSS_PATH:</strong> <code>" . CSS_PATH . "</code></p>\n";
echo "<p><strong>JS_PATH:</strong> <code>" . JS_PATH . "</code></p>\n";
echo "</div>\n";

// Verificar archivos críticos
echo "<h3>🔍 Verificación de Recursos Críticos</h3>\n";
echo "<div class='resource-grid'>\n";

$criticalResources = [
    'CSS Principal' => CSS_PATH . '/login.css',
    'CSS Moderno' => CSS_PATH . '/modern_theme.css',
    'CSS Registro' => CSS_PATH . '/registro_vehiculos.css',
    'CSS Inicio' => CSS_PATH . '/estilo_inicio.css',
    'CSS Footer' => CSS_PATH . '/estilos_footer.css',
    'JavaScript Principal' => JS_PATH . '/Inicio-Register.js'
];

foreach ($criticalResources as $name => $url) {
    echo "<div class='resource-card'>\n";
    echo "<h4>$name</h4>\n";
    echo "<p><strong>URL:</strong> <code>$url</code></p>\n";
    
    // Verificar si el archivo existe en el sistema de archivos
    $filePath = str_replace(BASE_URL, dirname(__DIR__), $url);
    $fileExists = file_exists($filePath);
    
    echo "<p><strong>Estado:</strong> ";
    if ($fileExists) {
        echo "<span style='color: #28a745;'>✅ Archivo encontrado</span>";
    } else {
        echo "<span style='color: #dc3545;'>❌ Archivo no encontrado</span>";
    }
    echo "</p>\n";
    
    echo "<a href='$url' target='_blank' class='test-link'>🔗 Probar enlace</a>\n";
    echo "</div>\n";
}

echo "</div>\n";

// Enlaces de prueba para páginas principales
echo "<h3>🔗 Enlaces de Prueba del Sistema</h3>\n";
echo "<div class='status info'>\n";
echo "<p>Prueba estos enlaces para verificar que el sistema funciona correctamente:</p>\n";
echo "<a href='" . BASE_URL . "/index.php' target='_blank' class='test-link'>🏠 Página Principal</a>\n";
echo "<a href='" . BASE_URL . "/estructura/views/inicio.php' target='_blank' class='test-link'>🔐 Login</a>\n";
echo "<a href='" . BASE_URL . "/estructura/views/registro_vehiculos.php' target='_blank' class='test-link'>🚗 Registro de Vehículos</a>\n";
echo "<a href='" . BASE_URL . "/estructura/views/salida_vehiculos.php' target='_blank' class='test-link'>🚪 Salida de Vehículos</a>\n";
echo "</div>\n";

// Mostrar ejemplos de uso en código
echo "<h3>💻 Ejemplos de Uso en el Código</h3>\n";
echo "<div class='status info'>\n";
echo "<h4>En archivos PHP:</h4>\n";
echo "<pre><code>";
echo htmlentities('<!-- Para incluir CSS -->' . "\n");
echo htmlentities('<link rel="stylesheet" href="<?php echo CSS_PATH; ?>/login.css">' . "\n\n");
echo htmlentities('<!-- Para incluir JavaScript -->' . "\n");
echo htmlentities('<script src="<?php echo JS_PATH; ?>/script.js"></script>' . "\n\n");
echo htmlentities('<!-- Para enlaces relativos -->' . "\n");
echo htmlentities('<a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php">Login</a>' . "\n");
echo "</code></pre>\n";
echo "</div>\n";

// Ventajas del sistema dinámico
echo "<h3>🌟 Ventajas del Sistema de Rutas Dinámicas</h3>\n";
echo "<div class='status success'>\n";
echo "<ul>\n";
echo "<li><strong>Portabilidad:</strong> El sistema funciona en cualquier servidor sin modificaciones</li>\n";
echo "<li><strong>Flexibilidad:</strong> Se adapta automáticamente a diferentes estructuras de directorios</li>\n";
echo "<li><strong>Mantenimiento:</strong> No es necesario cambiar URLs cuando se mueve el proyecto</li>\n";
echo "<li><strong>Entornos múltiples:</strong> Funciona igual en desarrollo, staging y producción</li>\n";
echo "<li><strong>HTTPS automático:</strong> Detecta automáticamente el protocolo correcto</li>\n";
echo "</ul>\n";
echo "</div>\n";

// Información técnica
echo "<h3>⚙️ Información Técnica</h3>\n";
echo "<div class='status info'>\n";
echo "<p><strong>Función de detección:</strong> <code>getBaseUrl()</code> en <code>estructura/config/config.php</code></p>\n";
echo "<p><strong>Variables detectadas:</strong></p>\n";
echo "<ul>\n";
echo "<li><code>\$_SERVER['HTTP_HOST']</code>: " . ($_SERVER['HTTP_HOST'] ?? 'No disponible') . "</li>\n";
echo "<li><code>\$_SERVER['SCRIPT_NAME']</code>: " . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "</li>\n";
echo "<li><code>\$_SERVER['HTTPS']</code>: " . ($_SERVER['HTTPS'] ?? 'No disponible') . "</li>\n";
echo "<li><code>\$_SERVER['SERVER_PORT']</code>: " . ($_SERVER['SERVER_PORT'] ?? 'No disponible') . "</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "<p style='text-align: center; margin-top: 30px; color: #6c757d;'>\n";
echo "Sistema de Estacionamientos UCT - Configuración de Rutas Dinámicas ✅\n";
echo "</p>\n";

echo "</div>\n";
echo "</body>\n";
echo "</html>\n";
?>
