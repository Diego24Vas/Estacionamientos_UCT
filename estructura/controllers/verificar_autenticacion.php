<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/models/Autenticador.php';
require_once dirname(__DIR__) . '/models/UserRepository.php';
require_once dirname(__DIR__) . '/config/conex.php';

// Crear instancia del repositorio de usuarios con decoradores
$baseRepository = new UserRepository($conexion);
$loggedRepository = new LoggingUserRepositoryDecorator($baseRepository);
$decoratedRepository = new CachingUserRepositoryDecorator($loggedRepository);

// Obtener instancia del Autenticador
$autenticador = Autenticador::getInstance($decoratedRepository);

// Verificar si el usuario está autenticado
$estaAutenticado = $autenticador->isAuthenticated();

// Generar un token CSRF para proteger formularios (logout, etc.)
$csrfToken = $autenticador->generarCSRFToken();

// Preparar respuesta
$respuesta = [
    "status" => "success",
    "authenticated" => $estaAutenticado,
    "csrf_token" => $csrfToken
];

// Si está autenticado, incluir datos del usuario
if ($estaAutenticado) {
    $respuesta["user"] = [
        "nombre" => $_SESSION['usuario'] ?? '',
        "id" => $_SESSION['user_id'] ?? '',
        "email" => $_SESSION['email'] ?? ''
    ];
}

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
exit;
?>
