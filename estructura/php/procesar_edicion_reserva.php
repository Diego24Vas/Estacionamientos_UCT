<?php
include('Reserva.php');
include('conex.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $evento = trim($_POST['evento']);
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFin = $_POST['hora_fin'];
    $zona = trim($_POST['zona']); 

    // Crear objeto de la reserva para actualizar
    $reserva = new Reserva($id, $evento, $fecha, $horaInicio, $horaFin, $zona);
    if ($reserva->actualizarReserva()) {
        echo "<script>alert('Reserva actualizada exitosamente.'); window.location.href = 'reservas.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la reserva.'); window.history.back();</script>";
    }
}
?>
