<?php
$host = "localhost";
$user = "root";
$password = "";
$BD = "a2024_dvasquez";
$conexion = new mysqli($host, $user, $password, $BD);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
// echo "Conexión exitosa!"; // Elimina o comenta esta línea
?>
