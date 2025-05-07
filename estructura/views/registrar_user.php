<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/conex.php';
require_once MODELS_PATH . '/classUser.php';

header('Content-Type: application/json');

// Verificar que el método sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre_usuario = trim($_POST['username'] ?? '');
    $correo = trim($_POST['email'] ?? '');
    $contrasena = $_POST['password'] ?? '';

    // Crear una instancia de la clase Usuario
    $usuario = new Usuario($conexion);

    // Llamar al método registrar
    $resultado = $usuario->registrar($nombre_usuario, $correo, $contrasena);

    // Devolver el resultado como JSON
    echo json_encode($resultado);
}
?>
