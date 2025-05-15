<?php
require_once MODELS_PATH . '/conex.php'; // Conexión a la base de datos

$query_reserved_spaces = "SELECT * FROM INFO1170_Estacionamiento WHERE Estado = 'Reservado' AND TIMESTAMPDIFF(HOUR, fecha_reserva, NOW()) >= 24";
$result_reserved_spaces = $conexion->query($query_reserved_spaces);

$remindersSent = false;
$message = '';
if ($result_reserved_spaces->num_rows > 0) {
    while ($row = $result_reserved_spaces->fetch_assoc()) {
        // Generar mensaje para la página
        $message .= "Recordatorio para liberar espacio reservado por " . $row['email'] . ".\n";
        $remindersSent = true;
    }
}

if ($remindersSent) {
    echo json_encode(['message' => $message]);
} else {
    echo json_encode(['message' => "No hay recordatorios para enviar."]);
}
?>
