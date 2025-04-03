<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('conex.php');

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST['username'] ?? '');
    $correo = trim($_POST['email'] ?? '');
    $contrasena = $_POST['password'] ?? '';

    if (empty($nombre_usuario) || empty($correo) || empty($contrasena)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "El correo proporcionado no es válido."]);
        exit();
    }

    $check_stmt = $conexion->prepare("SELECT id FROM INFO1170_RegistroUsuarios WHERE nombre = ? OR email = ?");
    $check_stmt->bind_param("ss", $nombre_usuario, $correo);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "El usuario o correo ya está registrado."]);
        $check_stmt->close();
        exit();
    }

    $check_stmt->close();
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("INSERT INTO INFO1170_RegistroUsuarios (nombre, email, contraseña) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre_usuario, $correo, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Registro exitoso"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar."]);
    }

    $stmt->close();
}

$conexion->close();
?>
