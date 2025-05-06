<?php
// Definición de rutas base
define('ROOT_PATH', dirname(__DIR__));
define('MODELS_PATH', ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('SERVICES_PATH', ROOT_PATH . '/services');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('HELPERS_PATH', ROOT_PATH . '/helpers');

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