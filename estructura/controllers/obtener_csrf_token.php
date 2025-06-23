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

// Generar token CSRF
$token = $autenticador->generarCSRFToken();

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "csrf_token" => $token,
    "csrf_field" => '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">'
]);
exit;
?>
