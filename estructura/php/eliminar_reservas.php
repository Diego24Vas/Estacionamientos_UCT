<?php
include('conex.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = $conexion->prepare("DELETE FROM INFO1170_Reservas WHERE id = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        echo json_encode(["status" => "success", "message" => "Reserva eliminada exitosamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar la reserva."]);
    }

    $query->close();
    $conexion->close();
} else {
    echo json_encode(["status" => "error", "message" => "ID de reserva no especificado o inválido."]);
}
?>
