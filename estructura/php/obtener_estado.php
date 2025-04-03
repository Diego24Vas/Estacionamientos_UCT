<?php
include('conex.php'); // Conexión a la base de datos

$query_max_occupancy = "SELECT COUNT(*) AS ocupados, (SELECT COUNT(*) FROM INFO1170_Estacionamiento) AS total FROM INFO1170_Estacionamiento WHERE Estado = 'Ocupado'";
$result_max_occupancy = $conexion->query($query_max_occupancy);
$data_max_occupancy = $result_max_occupancy->fetch_assoc();

$maxOccupancyReached = $data_max_occupancy['ocupados'] >= $data_max_occupancy['total'];

echo json_encode(['maxOccupancyReached' => $maxOccupancyReached]);
?>