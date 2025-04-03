<?php
include('Reserva.php');
include('conex.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Crear instancia de la clase Reserva y eliminar la reserva
    $reserva = new Reserva($id);
    if ($reserva->eliminarReserva()) {
        echo json_encode(["status" => "success", "message" => "Reserva eliminada exitosamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar la reserva."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID de reserva no especificado o inválido."]);
}
?>
 