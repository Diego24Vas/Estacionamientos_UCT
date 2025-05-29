<?php
// Definición de rutas base
define('ROOT_PATH', dirname(__DIR__));
define('MODELS_PATH', ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('SERVICES_PATH', ROOT_PATH . '/services');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('HELPERS_PATH', ROOT_PATH . '/helpers');

// Rutas para recursos estáticos
// Detectar automáticamente la URL base del proyecto
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
    // Usar el nombre del servidor en lugar de la IP
    $host = $_SERVER['HTTP_HOST'];
    
    // Si detectamos la IP del servidor universitario, usar el hostname
    if ($host === '172.24.250.129') {
        $host = 'pillan.inf.uct.cl';
    }
    
    // Obtener la ruta del script actual
    $scriptName = $_SERVER['SCRIPT_NAME'];
    
    // Detectar si estamos en la carpeta estructura o en la raíz
    if (strpos($scriptName, '/estructura/') !== false) {
        // Estamos dentro de la carpeta estructura
        $projectPath = substr($scriptName, 0, strpos($scriptName, '/estructura/'));
    } else {
        // Estamos en la raíz del proyecto
        $projectPath = dirname($scriptName);
    }
    
    // Asegurar que no termine con /
    $projectPath = rtrim($projectPath, '/');
    
    // Si la ruta está vacía, usar /
    if (empty($projectPath)) {
        $projectPath = '';
    }
    
    return $protocol . $host . $projectPath;
}

define('BASE_URL', getBaseUrl());
define('CSS_PATH', BASE_URL . '/estructura/views/css');
define('JS_PATH', BASE_URL . '/estructura/views/js');
define('IMAGES_PATH', VIEWS_PATH . '/images');

// Función para cargar clases automáticamente
spl_autoload_register(function ($class_name) {
    $paths = [
        MODELS_PATH,
        CONTROLLERS_PATH,
        SERVICES_PATH,
        HELPERS_PATH
    ];
    
    foreach ($paths as $path) {
        $file = $path . '/' . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Incluir archivo de conexión
require_once CONFIG_PATH . '/conex.php';

// Incluir funciones de ayuda
require_once HELPERS_PATH . '/functions.php';
