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
            $_SESSION['error'] = 'La acción solicitada no existe';
            header('Location: /error.php');
            exit();
        }
    } else {
        // Controlador no encontrado
        $_SESSION['error'] = 'El controlador solicitado no existe';
        header('Location: /error.php');
        exit();
    }
} else {
    // Archivo de controlador no encontrado
    $_SESSION['error'] = 'El archivo del controlador no existe';
    header('Location: /error.php');
    exit();
} 