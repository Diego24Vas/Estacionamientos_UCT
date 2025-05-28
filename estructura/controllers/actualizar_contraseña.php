<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classUser.php';
require_once CONFIG_PATH . '/conex.php';

header('Content-Type: application/json');

// Validar que los datos se reciban
if (empty($_POST['token']) || empty($_POST['nueva_contraseña'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

$token = $_POST['token'];
$nueva_contraseña = $_POST['nueva_contraseña'];

// Validar formato y fuerza mínima de la contraseña
if (strlen($nueva_contraseña) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe tener al menos 8 caracteres']);
    exit;
}

// Hashear la contraseña
$hash_contraseña = password_hash($nueva_contraseña, PASSWORD_BCRYPT);

// Validar el token y su fecha de expiración
$query = $mysqli->prepare("SELECT user_id FROM recuperacion_password WHERE token = ? AND expira > NOW()");
$query->bind_param("s", $token);
$query->execute();
$query->store_result();

if ($query->num_rows > 0) {
    $query->bind_result($user_id);
    $query->fetch();

    // Actualizar la contraseña en la tabla de usuarios
    $update = $mysqli->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
    $update->bind_param("si", $hash_contraseña, $user_id);

    if ($update->execute()) {
        // Eliminar el token después de usarlo
        $delete = $mysqli->prepare("DELETE FROM recuperacion_password WHERE token = ?");
        $delete->bind_param("s", $token);
        $delete->execute();

        http_response_code(200);
        echo json_encode(['mensaje' => 'Contraseña actualizada correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar la contraseña']);
    }

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Token inválido o expirado']);
}
?>