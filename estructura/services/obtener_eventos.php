<?php
include('conex.php');

// Consulta para obtener los eventos reservados
$query = "SELECT id, evento, fecha, hora_inicio, hora_fin, zona FROM INFO1170_Reservas ORDER BY fecha, hora_inicio";
$result = $conexion->query($query);

$eventos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($eventos);

$conexion->close();
?>
