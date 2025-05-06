<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';
include('conex.php'); // Asegúrate de tener la conexión a la base de datos disponible

// Crear una instancia de la clase Reserva
$reserva = new Reserva();
$reserva->id = $_GET['id'];  // Suponiendo que el ID de la reserva se pasa por GET

// Obtener la conexión
$conexion = new Conexion();
$conexion = $conexion->getConexion();

// Llamar al método para obtener la reserva por ID
$reserva_data = $reserva->obtenerReservaPorId($conexion);

// // Mostrar la información de la reserva
// echo "Evento: " . $reserva_data['evento'] . "<br>";
// echo "Fecha: " . $reserva_data['fecha'] . "<br>";
// echo "Hora de Inicio: " . $reserva_data['hora_inicio'] . "<br>";
// echo "Hora de Fin: " . $reserva_data['hora_fin'] . "<br>";
// echo "Zona: " . $reserva_data['zona'] . "<br>";
?>

