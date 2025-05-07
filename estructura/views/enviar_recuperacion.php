<?php
require_once MODELS_PATH . '/conex.php';
require_once __DIR__ . '/../config/config.php';

$email = $_POST['email'];

// Verificar si el email está registrado
$query = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$query->store_result();

if ($query->num_rows > 0) {
    $query->bind_result($user_id);
    $query->fetch();

    // Generar el token y la fecha de expiración
    $token = bin2hex(random_bytes(50));
    $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Guardar el token en la base de datos
    $insert = $mysqli->prepare("INSERT INTO INFO1170_RecuperacionPassword (user_id, token, expira) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $user_id, $token, $expira);
    $insert->execute();

    // Enviar el email de recuperación
    $link = "https://pillan.inf.uct.cl/       ~NOMBRE DE USUARIO       /      DIRECTORIO     /proyecto/Integracion1_version2/Integracion_I/estructura/resetear_contraseña.php?token=" . $token;
    $subject = "Recuperación de Contraseña";
    $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: $link";
    $headers = "From: no-reply@tu-sitio.com";

    mail($email, $subject, $message, $headers);

    echo "Hemos enviado un enlace de recuperación a tu email.";
} else {
    echo "Este email no está registrado.";
}
?>
