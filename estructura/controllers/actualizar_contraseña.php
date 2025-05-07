<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classUser.php';
require_once CONFIG_PATH . '/conex.php';

$token = $_POST['token'];
$nueva_contraseña = password_hash($_POST['nueva_contraseña'], PASSWORD_BCRYPT);

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
    $update->bind_param("si", $nueva_contraseña, $user_id);
    $update->execute();

    // Eliminar el token después de usarlo
    $delete = $mysqli->prepare("DELETE FROM recuperacion_password WHERE token = ?");
    $delete->bind_param("s", $token);
    $delete->execute();

    echo "Contraseña actualizada exitosamente.";
} else {
    echo "Token inválido o expirado.";
}
?>
