<?php
require_once 'config/config.php';
require_once 'services/session_manager.php';

// Crear carpeta de logs si no existe
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Registrar acceso para depuración
file_put_contents(
    $log_dir . '/estructura_index_access.log',
    date('Y-m-d H:i:s') . " - Acceso al index de estructura - Autenticado: " . (is_authenticated() ? 'Sí' : 'No') . PHP_EOL,
    FILE_APPEND
);

// Determinar si el usuario está autenticado
if (is_authenticated()) {
    // Si está autenticado, redirigir al panel principal
    $redirect_url = BASE_URL . "/estructura/views/pag_inicio.php";
    file_put_contents(
        $log_dir . '/estructura_index_redirect.log',
        date('Y-m-d H:i:s') . " - Usuario autenticado, redirigiendo a: " . $redirect_url . PHP_EOL,
        FILE_APPEND
    );
} else {
    // Si no está autenticado, redirigir a la página de inicio de sesión
    $redirect_url = BASE_URL . "/estructura/views/inicio.php";
    file_put_contents(
        $log_dir . '/estructura_index_redirect.log',
        date('Y-m-d H:i:s') . " - Usuario NO autenticado, redirigiendo a: " . $redirect_url . PHP_EOL,
        FILE_APPEND
    );
}

// Ejecutar la redirección
if (!headers_sent()) {
    header("Location: " . $redirect_url);
} else {
    echo '<script>window.location.href="' . htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8') . '";</script>';
}
exit();

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