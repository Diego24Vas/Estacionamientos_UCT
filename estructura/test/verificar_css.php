<?php
/**
 * Script simple para verificar acceso directo a archivos CSS
 * Este script ayuda a identificar problemas de acceso a recursos estáticos
 */

// Incluir configuración
require_once 'estructura/config/config.php';

// CSS de emergencia inline para que la página se vea bien
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Verificador de CSS - Estacionamientos UCT</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: rgba(255,255,255,0.1); 
            padding: 20px; 
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        .test-result { 
            margin: 10px 0; 
            padding: 10px; 
            border-radius: 5px; 
            background: rgba(255,255,255,0.2);
        }
        .success { background: rgba(40,167,69,0.3); }
        .error { background: rgba(220,53,69,0.3); }
        .url-test { 
            font-family: monospace; 
            background: rgba(0,0,0,0.3); 
            padding: 5px; 
            border-radius: 3px; 
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificador de Archivos CSS</h1>
        <p><strong>Servidor:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
        <p><strong>BASE_URL detectado:</strong> <?php echo BASE_URL; ?></p>
        
        <h2>📋 Verificación de Archivos CSS</h2>
        
        <?php
        // Lista de archivos CSS críticos
        $archivos_css = [
            'login.css' => 'CSS de Login',
            'modern_theme.css' => 'Tema Moderno',
            'registro_vehiculos.css' => 'Registro de Vehículos',
            'estilo_inicio.css' => 'Estilo de Inicio',
            'estilos_footer.css' => 'Footer',
            'styles.css' => 'Estilos Principales',
            'stylesnew.css' => 'Estilos Nuevos'
        ];
        
        foreach ($archivos_css as $archivo => $descripcion) {
            // Verificar si existe físicamente
            $ruta_fisica = ROOT_PATH . '/views/css/' . $archivo;
            $ruta_url = CSS_PATH . '/' . $archivo;
            
            echo "<div class='test-result " . (file_exists($ruta_fisica) ? 'success' : 'error') . "'>";
            echo "<h3>$descripcion ($archivo)</h3>";
            
            if (file_exists($ruta_fisica)) {
                $size = filesize($ruta_fisica);
                $permisos = substr(sprintf('%o', fileperms($ruta_fisica)), -4);
                echo "<p>✅ <strong>Archivo encontrado</strong></p>";
                echo "<p>📏 Tamaño: $size bytes</p>";
                echo "<p>🔐 Permisos: $permisos</p>";
                echo "<p class='url-test'>📁 Ruta física: $ruta_fisica</p>";
                echo "<p class='url-test'>🌐 URL: $ruta_url</p>";
                echo "<a href='$ruta_url' target='_blank' class='btn'>🔗 Probar enlace directo</a>";
            } else {
                echo "<p>❌ <strong>Archivo NO encontrado</strong></p>";
                echo "<p class='url-test'>📁 Buscado en: $ruta_fisica</p>";
                echo "<p class='url-test'>🌐 URL esperada: $ruta_url</p>";
            }
            
            echo "</div>";
        }
        ?>
        
        <h2>📂 Explorador de Directorio CSS</h2>
        <div class="test-result">
            <?php
            $css_dir = ROOT_PATH . '/views/css';
            if (is_dir($css_dir)) {
                echo "<p>✅ Directorio CSS encontrado: <code>$css_dir</code></p>";
                
                $archivos = scandir($css_dir);
                $archivos_css_encontrados = array_filter($archivos, function($file) {
                    return pathinfo($file, PATHINFO_EXTENSION) === 'css';
                });
                
                if (empty($archivos_css_encontrados)) {
                    echo "<p>⚠️ No se encontraron archivos .css en el directorio</p>";
                } else {
                    echo "<p>📋 Archivos CSS encontrados:</p>";
                    echo "<ul>";
                    foreach ($archivos_css_encontrados as $archivo) {
                        $ruta_completa = $css_dir . '/' . $archivo;
                        $size = filesize($ruta_completa);
                        $url = CSS_PATH . '/' . $archivo;
                        echo "<li><strong>$archivo</strong> ($size bytes) - <a href='$url' target='_blank' class='btn'>Probar</a></li>";
                    }
                    echo "</ul>";
                }
                
                // Mostrar TODOS los archivos del directorio (para debug)
                echo "<details>";
                echo "<summary>🔍 Ver todos los archivos del directorio (debug)</summary>";
                $todos_archivos = array_diff($archivos, ['.', '..']);
                if (empty($todos_archivos)) {
                    echo "<p>El directorio está vacío</p>";
                } else {
                    echo "<ul>";
                    foreach ($todos_archivos as $archivo) {
                        $ruta_completa = $css_dir . '/' . $archivo;
                        if (is_file($ruta_completa)) {
                            $size = filesize($ruta_completa);
                            $ext = pathinfo($archivo, PATHINFO_EXTENSION);
                            echo "<li>$archivo ($ext, $size bytes)</li>";
                        } else {
                            echo "<li>$archivo (directorio)</li>";
                        }
                    }
                    echo "</ul>";
                }
                echo "</details>";
            } else {
                echo "<p>❌ Directorio CSS NO encontrado: <code>$css_dir</code></p>";
                
                // Intentar encontrar dónde están los archivos CSS
                echo "<h3>🔍 Búsqueda de archivos CSS en el proyecto:</h3>";
                $possible_paths = [
                    ROOT_PATH . '/css',
                    ROOT_PATH . '/assets/css',
                    ROOT_PATH . '/public/css',
                    ROOT_PATH . '/static/css',
                    dirname(ROOT_PATH) . '/css',
                    dirname(ROOT_PATH) . '/assets/css'
                ];
                
                foreach ($possible_paths as $path) {
                    if (is_dir($path)) {
                        $css_files = glob($path . '/*.css');
                        if (!empty($css_files)) {
                            echo "<p>✅ Archivos CSS encontrados en: <code>$path</code></p>";
                            echo "<ul>";
                            foreach ($css_files as $file) {
                                echo "<li>" . basename($file) . "</li>";
                            }
                            echo "</ul>";
                        }
                    }
                }
            }
            ?>
        </div>
        
        <h2>⚡ Prueba de Velocidad</h2>
        <div class="test-result">
            <?php
            $start = microtime(true);
            
            // Simulamos carga de un archivo CSS
            $test_file = ROOT_PATH . '/views/css/login.css';
            if (file_exists($test_file)) {
                $content = file_get_contents($test_file);
                $load_time = (microtime(true) - $start) * 1000;
                echo "<p>✅ Tiempo de carga del archivo CSS: <strong>" . number_format($load_time, 2) . " ms</strong></p>";
                echo "<p>📏 Tamaño del contenido: " . strlen($content) . " caracteres</p>";
            } else {
                echo "<p>⚠️ No se pudo hacer la prueba de velocidad (archivo no encontrado)</p>";
            }
            
            // Información del servidor
            echo "<p>🖥️ Versión PHP: " . phpversion() . "</p>";
            echo "<p>💾 Memoria usada: " . number_format(memory_get_usage(true) / 1024 / 1024, 2) . " MB</p>";
            ?>
        </div>
        
        <h2>🛠️ Posibles Soluciones</h2>
        <div class="test-result">
            <h3>Si los archivos CSS no se cargan:</h3>
            <ol>
                <li><strong>Verificar subida:</strong> Asegúrate de que todos los archivos CSS estén subidos al servidor</li>
                <li><strong>Permisos:</strong> Ejecutar <code>chmod 644 *.css</code> en el directorio CSS</li>
                <li><strong>Configuración del servidor:</strong> Verificar que Apache/Nginx sirva archivos .css correctamente</li>
                <li><strong>Cache del navegador:</strong> Usar Ctrl+F5 para forzar recarga</li>
                <li><strong>Firewall/Proxy:</strong> Verificar si hay restricciones institucionales</li>
            </ol>
            
            <h3>Para mejorar la velocidad:</h3>
            <ol>
                <li><strong>Minificar CSS:</strong> Usar herramientas para comprimir archivos CSS</li>
                <li><strong>Combinar archivos:</strong> Unir múltiples CSS en uno solo</li>
                <li><strong>Cache HTTP:</strong> Configurar headers de cache en el servidor</li>
                <li><strong>Usar CDN:</strong> Para frameworks como Bootstrap</li>
            </ol>
        </div>
        
        <h2>🔗 Enlaces Útiles</h2>
        <div class="test-result">
            <a href="<?php echo BASE_URL; ?>" class="btn">🏠 Ir a la página principal</a>
            <a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php" class="btn">🔐 Página de login</a>
            <a href="diagnosticar_servidor.php" class="btn">🔧 Diagnóstico completo</a>
            <a href="test_base_url.php" class="btn">✅ Pruebas de rutas</a>
        </div>
    </div>
</body>
</html>
