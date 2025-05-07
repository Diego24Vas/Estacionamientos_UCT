<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/conex.php'; // Conexión a la base de datos

header('Content-Type: application/json');

// Comprobar la conexión
if ($conexion->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conexion->connect_error]);
    exit();
}

// Consulta de marcas
$query = "SELECT id, nombre_marca AS name FROM INFO1170_MarcasVehiculos";
$result = $conexion->query($query);

$brands = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
} else {
    echo json_encode(["error" => "No se encontraron marcas en la base de datos"]);
    exit();
}

// Enviar las marcas en formato JSON
echo json_encode($brands);
?>
