<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el gestor de sesiones antes de cualquier salida HTML
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';
require_once dirname(__DIR__) . '/models/Autenticador.php';
require_once dirname(__DIR__) . '/models/UserRepository.php';
require_once dirname(__DIR__) . '/config/conex.php';

// Crear instancia del repositorio de usuarios con decoradores
$baseRepository = new UserRepository($conexion);
$loggedRepository = new LoggingUserRepositoryDecorator($baseRepository);
$decoratedRepository = new CachingUserRepositoryDecorator($loggedRepository);

// Obtener instancia del Autenticador
$autenticador = Autenticador::getInstance($decoratedRepository);

// Verificar si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Intentar iniciar sesión
    $resultado = $autenticador->login($username, $password);
    
    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
} else {
    // Si no es POST, redirigir a la página de login
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Método no permitido"
    ]);
    exit;
}
?>

