<?php
include('../classReserva.php');
include('conex.php'); // Asegúrate de tener la conexión a la base de datos disponible

// Crear una instancia de la clase Reserva
$reserva = new Reserva();

// Obtener la conexión
$conexion = new Conexion();
$conexion = $conexion->getConexion();

// Pasar los datos recibidos en el formulario
$reserva->evento = $_POST['evento'];
$reserva->fecha = $_POST['fecha'];
$reserva->horaInicio = $_POST['horaInicio'];
$reserva->horaFin = $_POST['horaFin'];
$reserva->zona = $_POST['zona'];

// Llamar al método para crear una reserva
$reserva->crearReserva($conexion);

echo "Reserva creada correctamente.";
?>
