<?php
include('conex.php');

// Verifica si se proporciona un ID en la solicitud
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitiza el ID recibido
    $query = $conexion->prepare("SELECT id, evento, fecha, hora_inicio, hora_fin, zona FROM INFO1170_Reservas WHERE id = ?");
    $query->bind_param("i", $id); // Vincula el ID a la consulta
    $query->execute();
    $result = $query->get_result();

    // Verifica si se encontró una reserva
    if ($result->num_rows > 0) {
        $reserva = $result->fetch_assoc(); // Obtiene la reserva como un arreglo asociativo
        echo json_encode($reserva); // Devuelve los datos en formato JSON
    } else {
        echo json_encode(["error" => "Reserva no encontrada"]); // Mensaje de error si no se encuentra la reserva
    }

    $query->close();
    $conexion->close();
} else {
    echo json_encode(["error" => "ID no especificado"]); // Mensaje de error si no se proporciona un ID
}
?>
