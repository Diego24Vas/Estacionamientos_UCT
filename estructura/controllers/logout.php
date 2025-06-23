<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Registrar este acceso para depuración
file_put_contents(
    dirname(__DIR__) . '/logs/logout_access.log', 
    date('Y-m-d H:i:s') . " - Logout accedido desde: " . ($_SERVER['HTTP_REFERER'] ?? 'Desconocido') . PHP_EOL,
    FILE_APPEND
);

// Incluir el gestor de sesiones antes de cualquier salida HTML
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';

// Crear carpeta de logs si no existe
$log_dir = dirname(__DIR__) . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Registrar información del usuario antes de cerrar sesión
if (isset($_SESSION['usuario'])) {
    file_put_contents(
        $log_dir . '/logout_users.log',
        date('Y-m-d H:i:s') . " - Usuario: " . $_SESSION['usuario'] . " - IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL,
        FILE_APPEND
    );
}

// Usar la función directa de cerrar sesión
logout_user();

// Determinar el formato de respuesta basado en la cabecera Accept
$wantsJson = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

if ($wantsJson) {
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "message" => "Sesión cerrada correctamente"
    ]);
} else {
    // Redirigir a la página de inicio
    $redirect_url = BASE_URL . '/estructura/views/inicio.php';
    
    // Registrar la URL de redirección para depuración
    file_put_contents(
        $log_dir . '/logout_redirects.log',
        date('Y-m-d H:i:s') . " - Redirigiendo a: " . $redirect_url . PHP_EOL,
        FILE_APPEND
    );
    
    if (!headers_sent()) {
        header("Location: " . $redirect_url);
    } else {
        echo '<script>window.location.href="' . htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8') . '";</script>';
    }
}
exit;
?>