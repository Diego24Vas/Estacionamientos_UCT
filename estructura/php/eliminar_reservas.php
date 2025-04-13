<?php
include('../NuevaEstructura/classReserva.php');

include('conex.php'); 

// Crear una instancia de la clase Reserva
$reserva = new Reserva();
$reserva->id = $_GET['id'];  

// Obtener la conexión
$conexion = new Conexion();
$conexion = $conexion->getConexion();


$reserva->eliminarReserva($conexion);

echo "Reserva eliminada con éxito.";
?>

 