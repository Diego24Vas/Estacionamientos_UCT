<?php
// Diagnóstico específico para el error HTTP 500 en reservas.php

// Activar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Error HTTP 500 en reservas.php</h1>";
echo "<hr>";

// 1. Verificar si config.php se puede cargar
echo "<h2>1. Verificando config.php</h2>";
try {
    include_once('estructura/config/config.php');
    echo "✅ config.php cargado correctamente<br>";
    
    // Mostrar constantes definidas
    echo "<strong>Constantes definidas:</strong><br>";
    if (defined('BASE_URL')) {
        echo "BASE_URL: " . BASE_URL . "<br>";
    } else {
        echo "❌ BASE_URL no está definida<br>";
    }
    
    if (defined('VIEWS_PATH')) {
        echo "VIEWS_PATH: " . VIEWS_PATH . "<br>";
    } else {
        echo "❌ VIEWS_PATH no está definida<br>";
    }
    
    if (defined('CSS_PATH')) {
        echo "CSS_PATH: " . CSS_PATH . "<br>";
    } else {
        echo "❌ CSS_PATH no está definida<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error cargando config.php: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 2. Verificar que cabecera.php existe y se puede cargar
echo "<h2>2. Verificando cabecera.php</h2>";
$cabecera_path = 'estructura/views/components/cabecera.php';
if (file_exists($cabecera_path)) {
    echo "✅ cabecera.php existe en: $cabecera_path<br>";
    
    // Intentar incluir cabecera.php con manejo de errores
    try {
        echo "<strong>Intentando cargar cabecera.php...</strong><br>";
        ob_start();
        include($cabecera_path);
        $cabecera_output = ob_get_clean();
        echo "✅ cabecera.php se cargó sin errores<br>";
    } catch (Exception $e) {
        echo "❌ Error cargando cabecera.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ cabecera.php no encontrado en: $cabecera_path<br>";
}

echo "<hr>";

// 3. Verificar rutas del CSS y recursos
echo "<h2>3. Verificando recursos CSS y estáticos</h2>";
$css_path = 'estructura/views/css/estilo_reservas.css';
if (file_exists($css_path)) {
    echo "✅ estilo_reservas.css existe<br>";
} else {
    echo "❌ estilo_reservas.css no encontrado<br>";
}

$stylesnew_path = 'estructura/views/css/stylesnew.css';
if (file_exists($stylesnew_path)) {
    echo "✅ stylesnew.css existe<br>";
} else {
    echo "❌ stylesnew.css no encontrado<br>";
}

$logo_path = 'estructura/img/logo.png';
if (file_exists($logo_path)) {
    echo "✅ logo.png existe<br>";
} else {
    echo "❌ logo.png no encontrado<br>";
}

echo "<hr>";

// 4. Verificar archivos JavaScript
echo "<h2>4. Verificando archivos JavaScript</h2>";
$js_reservas_path = 'estructura/views/js/reservas.js';
if (file_exists($js_reservas_path)) {
    echo "✅ reservas.js existe<br>";
} else {
    echo "❌ reservas.js no encontrado<br>";
}

echo "<hr>";

// 5. Verificar pie.php
echo "<h2>5. Verificando pie.php</h2>";
$pie_path = 'estructura/views/pie.php';
if (file_exists($pie_path)) {
    echo "✅ pie.php existe<br>";
} else {
    echo "❌ pie.php no encontrado<br>";
}

echo "<hr>";

// 6. Simular carga de reservas.php con manejo de errores detallado
echo "<h2>6. Simulando carga de reservas.php</h2>";
try {
    echo "<strong>Definiendo constantes requeridas...</strong><br>";
    
    if (!defined('VIEWS_PATH')) {
        define('VIEWS_PATH', 'estructura/views');
        echo "Definida VIEWS_PATH: " . VIEWS_PATH . "<br>";
    }
    
    if (!defined('BASE_URL')) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $current_dir = dirname($_SERVER['SCRIPT_NAME']);
        if ($host === '172.24.250.129') {
            $host = 'pillan.inf.uct.cl';
        }
        $base_url = $protocol . '://' . $host . '/~dprado/Estacionamientos_UCT';
        define('BASE_URL', $base_url);
        echo "Definida BASE_URL: " . BASE_URL . "<br>";
    }
    
    if (!defined('CSS_PATH')) {
        define('CSS_PATH', BASE_URL . '/estructura/views/css');
        echo "Definida CSS_PATH: " . CSS_PATH . "<br>";
    }
    
    echo "<strong>Intentando cargar reservas.php...</strong><br>";
    
    // Capturar cualquier error de sintaxis o ejecución
    $reservas_path = 'estructura/views/reservas.php';
    if (file_exists($reservas_path)) {
        echo "✅ reservas.php existe<br>";
        
        // Verificar sintaxis PHP
        $syntax_check = shell_exec("php -l $reservas_path 2>&1");
        if (strpos($syntax_check, 'No syntax errors') !== false) {
            echo "✅ Sintaxis PHP válida en reservas.php<br>";
        } else {
            echo "❌ Error de sintaxis en reservas.php:<br>";
            echo "<pre>$syntax_check</pre>";
        }
    } else {
        echo "❌ reservas.php no encontrado<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error simulando carga: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 7. Verificar configuración del servidor
echo "<h2>7. Configuración del servidor</h2>";
echo "Error reporting: " . error_reporting() . "<br>";
echo "Display errors: " . ini_get('display_errors') . "<br>";
echo "Log errors: " . ini_get('log_errors') . "<br>";
echo "Error log: " . ini_get('error_log') . "<br>";

echo "<hr>";
echo "<h2>Completado</h2>";
echo "Si ve este mensaje, el diagnóstico se ejecutó completamente.";
?>
