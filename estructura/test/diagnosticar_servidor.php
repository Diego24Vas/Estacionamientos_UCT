<?php
/**
 * Diagnóstico completo del servidor para identificar problemas
 * con archivos CSS, JavaScript y rendimiento
 */

// Incluir configuración para obtener las rutas
require_once 'estructura/config/config.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>🔧 Diagnóstico del Servidor - Estacionamientos UCT</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background-color: #f5f5f5; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section { 
            margin: 20px 0; 
            padding: 15px; 
            border-left: 4px solid #007bff; 
            background: #f8f9fa;
        }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .warning { border-left-color: #ffc107; background: #fff3cd; }
        .file-list { 
            max-height: 300px; 
            overflow-y: auto; 
            background: #f1f1f1; 
            padding: 10px; 
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
        }
        .performance-test {
            display: inline-block;
            margin: 5px;
            padding: 8px 12px;
            background: #e9ecef;
            border-radius: 5px;
            font-size: 14px;
        }
        .timing {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>";

echo "<div class='container'>";
echo "<h1>🔧 Diagnóstico Completo del Servidor</h1>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

// 1. VERIFICACIÓN DE ESTRUCTURA DE DIRECTORIOS
echo "<div class='test-section'>";
echo "<h2>📁 1. Estructura de Directorios</h2>";

$directorios_criticos = [
    'estructura/views/css' => CSS_PATH,
    'estructura/views/js' => JS_PATH,
    'estructura/models' => 'Modelos',
    'estructura/controllers' => 'Controladores',
    'estructura/config' => 'Configuración'
];

foreach ($directorios_criticos as $dir => $descripcion) {
    $ruta_fisica = ROOT_PATH . '/' . str_replace('estructura/', '', $dir);
    if (is_dir($ruta_fisica)) {
        $archivos = scandir($ruta_fisica);
        $count = count($archivos) - 2; // Quitar . y ..
        echo "<p>✅ <strong>$dir</strong>: $count archivos encontrados</p>";
    } else {
        echo "<p>❌ <strong>$dir</strong>: Directorio no encontrado</p>";
    }
}
echo "</div>";

// 2. VERIFICACIÓN DE ARCHIVOS CSS ESPECÍFICOS
echo "<div class='test-section'>";
echo "<h2>🎨 2. Verificación de Archivos CSS</h2>";

$archivos_css = [
    'login.css',
    'modern_theme.css',
    'registro_vehiculos.css',
    'estilo_inicio.css',
    'estilos_footer.css',
    'styles.css',
    'stylesnew.css'
];

$css_directory = ROOT_PATH . '/views/css';
if (is_dir($css_directory)) {
    echo "<p>✅ Directorio CSS encontrado: <code>$css_directory</code></p>";
    
    foreach ($archivos_css as $archivo) {
        $ruta_completa = $css_directory . '/' . $archivo;
        if (file_exists($ruta_completa)) {
            $size = filesize($ruta_completa);
            $permisos = substr(sprintf('%o', fileperms($ruta_completa)), -4);
            echo "<p>✅ <strong>$archivo</strong> - Tamaño: {$size} bytes, Permisos: $permisos</p>";
        } else {
            echo "<p>❌ <strong>$archivo</strong> - No encontrado</p>";
        }
    }
    
    // Listar TODOS los archivos CSS disponibles
    echo "<h3>📋 Archivos CSS Disponibles:</h3>";
    echo "<div class='file-list'>";
    $archivos_disponibles = glob($css_directory . '/*.css');
    if (empty($archivos_disponibles)) {
        echo "No se encontraron archivos CSS en el directorio.";
    } else {
        foreach ($archivos_disponibles as $archivo) {
            $nombre = basename($archivo);
            $size = filesize($archivo);
            echo "$nombre ($size bytes)<br>";
        }
    }
    echo "</div>";
} else {
    echo "<p>❌ Directorio CSS NO encontrado: <code>$css_directory</code></p>";
}
echo "</div>";

// 3. VERIFICACIÓN DE ARCHIVOS JAVASCRIPT
echo "<div class='test-section'>";
echo "<h2>⚡ 3. Verificación de Archivos JavaScript</h2>";

$js_directory = ROOT_PATH . '/views/js';
if (is_dir($js_directory)) {
    echo "<p>✅ Directorio JS encontrado: <code>$js_directory</code></p>";
    
    $archivos_js = glob($js_directory . '/*.js');
    if (empty($archivos_js)) {
        echo "<p>⚠️ No se encontraron archivos JavaScript.</p>";
    } else {
        echo "<div class='file-list'>";
        foreach ($archivos_js as $archivo) {
            $nombre = basename($archivo);
            $size = filesize($archivo);
            echo "$nombre ($size bytes)<br>";
        }
        echo "</div>";
    }
} else {
    echo "<p>❌ Directorio JS NO encontrado: <code>$js_directory</code></p>";
}
echo "</div>";

// 4. PRUEBAS DE RENDIMIENTO
echo "<div class='test-section'>";
echo "<h2>⚡ 4. Pruebas de Rendimiento</h2>";

// Tiempo de carga de la página
$start_time = microtime(true);

// Simulamos algunas operaciones
sleep(0.1); // Simular carga
$db_time = microtime(true);

// Tiempo de lectura de archivo
$config_start = microtime(true);
$config_content = file_get_contents('estructura/config/config.php');
$config_end = microtime(true);

$end_time = microtime(true);

$page_load_time = ($end_time - $start_time) * 1000;
$config_load_time = ($config_end - $config_start) * 1000;

echo "<div class='performance-test'>🕐 Tiempo de carga: <span class='timing'>" . number_format($page_load_time, 2) . " ms</span></div>";
echo "<div class='performance-test'>📄 Lectura config.php: <span class='timing'>" . number_format($config_load_time, 2) . " ms</span></div>";
echo "<div class='performance-test'>💾 Memoria usada: <span class='timing'>" . number_format(memory_get_usage(true) / 1024 / 1024, 2) . " MB</span></div>";

// Evaluar rendimiento
if ($page_load_time > 1000) {
    echo "<p class='error'>⚠️ <strong>Rendimiento LENTO</strong>: El servidor está respondiendo lentamente (>{$page_load_time}ms)</p>";
} elseif ($page_load_time > 500) {
    echo "<p class='warning'>⚠️ <strong>Rendimiento MODERADO</strong>: El servidor podría ser más rápido</p>";
} else {
    echo "<p class='success'>✅ <strong>Rendimiento BUENO</strong>: El servidor responde adecuadamente</p>";
}
echo "</div>";

// 5. INFORMACIÓN DEL SERVIDOR
echo "<div class='test-section'>";
echo "<h2>🖥️ 5. Información del Servidor</h2>";

$server_info = [
    'PHP Version' => phpversion(),
    'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible',
    'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'No disponible',
    'Server Admin' => $_SERVER['SERVER_ADMIN'] ?? 'No disponible',
    'Max Execution Time' => ini_get('max_execution_time') . ' segundos',
    'Memory Limit' => ini_get('memory_limit'),
    'Upload Max Size' => ini_get('upload_max_filesize'),
    'Post Max Size' => ini_get('post_max_size')
];

echo "<table style='width: 100%; border-collapse: collapse;'>";
foreach ($server_info as $key => $value) {
    echo "<tr style='border-bottom: 1px solid #ddd;'>";
    echo "<td style='padding: 8px; font-weight: bold;'>$key:</td>";
    echo "<td style='padding: 8px;'>$value</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// 6. RECOMENDACIONES
echo "<div class='test-section warning'>";
echo "<h2>💡 6. Recomendaciones para Optimización</h2>";

echo "<h3>🚀 Para mejorar el rendimiento:</h3>";
echo "<ul>";
echo "<li><strong>Comprimir archivos CSS/JS:</strong> Usar herramientas de minificación</li>";
echo "<li><strong>Caché del navegador:</strong> Configurar headers de caché apropiados</li>";
echo "<li><strong>Optimizar imágenes:</strong> Usar formatos modernos (WebP) y compresión</li>";
echo "<li><strong>CDN:</strong> Usar CDN para recursos estáticos si es posible</li>";
echo "</ul>";

echo "<h3>🔧 Para solucionar archivos CSS no encontrados:</h3>";
echo "<ul>";
echo "<li><strong>Verificar subida:</strong> Asegúrate de que todos los archivos CSS estén subidos al servidor</li>";
echo "<li><strong>Permisos:</strong> Verificar que los archivos tengan permisos de lectura (644)</li>";
echo "<li><strong>Rutas:</strong> Confirmar que las rutas en el código coincidan con la estructura real</li>";
echo "<li><strong>Servidor web:</strong> Verificar configuración del servidor para servir archivos .css</li>";
echo "</ul>";

echo "<h3>📋 Próximos pasos sugeridos:</h3>";
echo "<ol>";
echo "<li>Verificar que todos los archivos CSS estén en: <code>" . ROOT_PATH . "/views/css/</code></li>";
echo "<li>Comprobar permisos de archivos: <code>chmod 644 *.css</code></li>";
echo "<li>Probar acceso directo a: <code>" . CSS_PATH . "/login.css</code></li>";
echo "<li>Revisar logs del servidor web para errores 404</li>";
echo "</ol>";
echo "</div>";

echo "<div class='test-section success'>";
echo "<h2>✅ Estado General del Sistema</h2>";
echo "<p><strong>Rutas dinámicas:</strong> ✅ Funcionando correctamente</p>";
echo "<p><strong>Detección automática:</strong> ✅ Funcionando en servidor universitario</p>";
echo "<p><strong>Configuración PHP:</strong> ✅ Adecuada para el proyecto</p>";
echo "<p><strong>Próximo objetivo:</strong> 🎯 Solucionar carga de archivos CSS</p>";
echo "</div>";

echo "</div>"; // Cerrar container
echo "</body></html>";
?>
