<?php
require_once 'config/config.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinar qué controlador y acción usar
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'inicio';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Construir el nombre del controlador
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = CONTROLLERS_PATH . '/' . $controller . '.php';

// Verificar si el controlador existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName();
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            // Acción no encontrada
            header('Location: ' . VIEWS_PATH . '/error.php');
        }
    } else {
        // Controlador no encontrado
        header('Location: ' . VIEWS_PATH . '/error.php');
    }
} else {
    // Archivo de controlador no encontrado
    header('Location: ' . VIEWS_PATH . '/error.php');
} 