<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('conex.php');
include('../NuevaEstructura/classUser.php');

header('Content-Type: application/json');

// Verificar que el método sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Crear una instancia de la clase Usuario
    $usuario = new Usuario($conexion);

    // Llamar al método iniciarSesion
    $resultado = $usuario->iniciarSesion($username, $password);

    // Devolver el resultado como JSON
    echo json_encode($resultado);
}
?>

