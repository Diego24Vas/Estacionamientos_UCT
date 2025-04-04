<?php
include('../classReserva.php');
include('conex.php'); // Asegúrate de tener la conexión a la base de datos disponible

// Crear una instancia de la clase Reserva
$reserva = new Reserva();
$reserva->id = $_POST['id']; // Suponiendo que el ID de la reserva se pasa por POST

// Obtener la conexión
$conexion = new Conexion();
$conexion = $conexion->getConexion();

// Pasar los nuevos datos recibidos en el formulario
$reserva->evento = $_POST['evento'];
$reserva->fecha = $_POST['fecha'];
$reserva->horaInicio = $_POST['horaInicio'];
$reserva->horaFin = $_POST['horaFin'];
$reserva->zona = $_POST['zona'];

// Llamar al método para actualizar la reserva
$reserva->actualizarReserva($conexion);

echo "Reserva actualizada correctamente.";
?>
