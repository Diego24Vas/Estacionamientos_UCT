<?php
include('conex.php'); // Conexión a la base de datos

if (isset($_GET['zone'])) {
    $zone = $_GET['zone'];

    // Validar que la zona esté entre las zonas válidas
    $zonas_validas = ['Zona A', 'Zona B', 'Zona C', 'Zona D']; // Aquí agregas todas las zonas posibles
    if (in_array($zone, $zonas_validas)) {
        // Consultar los espacios disponibles en la zona seleccionada
        $query = "SELECT IdEstacionamiento, Ubicacion FROM INFO1170_Estacionamiento WHERE Ubicacion = ? AND Estado = 'Disponible'";
        $stmt = $conexion->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $zone);
            $stmt->execute();
            $result = $stmt->get_result();

            // Obtener los datos y devolverlos como JSON
            $spaces = [];
            while ($row = $result->fetch_assoc()) {
                $spaces[] = $row;
            }

            // Si no hay espacios disponibles
            if (empty($spaces)) {
                echo json_encode(['error' => 'No hay espacios disponibles en esta zona.']);
            } else {
                echo json_encode($spaces);
            }

            $stmt->close();
        } else {
            echo json_encode(['error' => 'Error al preparar la consulta.']);
        }
    } else {
        echo json_encode(['error' => 'Zona inválida.']);
    }
} else {
    echo json_encode(['error' => 'Zona no especificada.']);
}
?>
