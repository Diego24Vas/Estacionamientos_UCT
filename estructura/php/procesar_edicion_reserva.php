<?php
include('conex.php');

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id']) && !empty($data['id'])) {
    $id = intval($data['id']);
    $evento = trim($data['evento']);
    $fecha = $data['fecha'];
    $horaInicio = $data['horaInicio'];
    $horaFin = $data['horaFin'];
    $zona = trim($data['zona']);

    // Actualizar la reserva en la base de datos
    $query = $conexion->prepare("UPDATE INFO1170_Reservas SET evento = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, zona = ? WHERE id = ?");
    $query->bind_param("sssssi", $evento, $fecha, $horaInicio, $horaFin, $zona, $id);

    if ($query->execute()) {
        echo json_encode(["status" => "success", "message" => "Reserva actualizada exitosamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar la reserva."]);
    }

    $query->close();
} else {
    echo json_encode(["status" => "error", "message" => "ID de reserva no especificado o inválido."]);
}

$conexion->close();
?>
