<?php
include('Reserva.php');
include('conex.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evento = trim($_POST['evento']);
    $fecha = $_POST['fecha'];
    $horaInicio = $_POST['hora_inicio'];
    $horaFin = $_POST['hora_fin'];
    $zona = trim($_POST['zona']); 

    // Verificar si la reserva ya existe
    $reserva = new Reserva(null, $evento, $fecha, $horaInicio, $horaFin, $zona);
    if ($reserva->validarReserva()) {
        echo "<script>alert('La zona ya está reservada para esa fecha y hora.'); window.history.back();</script>";
    } else {
        // Crear la nueva reserva
        if ($reserva->crearReserva()) {
            echo "<script>alert('Reserva creada exitosamente.'); window.location.href = 'reservas.php';</script>";
        } else {
            echo "<script>alert('Error al crear la reserva.'); window.history.back();</script>";
        }
    }
}
?>
