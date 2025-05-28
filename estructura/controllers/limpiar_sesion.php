<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/controllers/UsuarioController.php';

// Crear instancia del controlador
$usuarioController = new UsuarioController();

// Manejar la acción de limpiar sesión
$usuarioController->handleRequest('limpiar-sesion');

?>