<?php
include('conex.php');

// Obtener la fecha actual y calcular el rango (hoy + 1 y hoy + 2 días)
$fecha_actual = date('Y-m-d');
$fecha_un_dia = date('Y-m-d', strtotime('+1 day'));
$fecha_dos_dias = date('Y-m-d', strtotime('+2 day'));

// Consulta para obtener eventos en los próximos 1 o 2 días
$query = $conexion->prepare("SELECT evento, fecha, hora_inicio, zona FROM INFO1170_Reservas WHERE fecha BETWEEN ? AND ?");
$query->bind_param("ss", $fecha_actual, $fecha_dos_dias);
$query->execute();
$result = $query->get_result();

$eventos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }
}

// Retornar los eventos en formato JSON
header('Content-Type: application/json');
echo json_encode($eventos);

$query->close();
$conexion->close();
?>
