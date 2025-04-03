<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('conex.php');

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validación básica
    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos."]);
        exit();
    }

    // Preparar consulta para verificar usuario
    $stmt = $conexion->prepare("SELECT contraseña FROM INFO1170_RegistroUsuarios WHERE nombre = ?");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta: " . $conexion->error]);
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    // Verificar si el usuario existe y la contraseña es correcta
    if ($hashedPassword && password_verify($password, $hashedPassword)) {
        session_start();
        $_SESSION['usuario'] = $username;
        echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso"]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Usuario o contraseña incorrectos."]);
    }

    $stmt->close();
}

$conexion->close();
?>
