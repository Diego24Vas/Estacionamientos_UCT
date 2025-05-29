<?php
// Procesar registro de vehículo - Versión simplificada
session_start();

// Incluir conexión a la base de datos
require_once('../config/conex.php');

try {
    // Verificar que se enviaron todos los datos requeridos
    $campos_requeridos = ['owner_first_name', 'owner_last_name', 'owner_email', 'vehicle_plate', 'vehicle_type', 'vehicle_brand', 'vehicle_model', 'zone_filter', 'user_type'];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            throw new Exception("El campo '$campo' es requerido");
        }
    }
    
    // Obtener datos del formulario
    $owner_first_name = trim($_POST['owner_first_name']);
    $owner_last_name = trim($_POST['owner_last_name']);
    $owner_email = trim($_POST['owner_email']);
    $owner_phone = trim($_POST['owner_phone'] ?? '');
    $vehicle_plate = strtoupper(trim($_POST['vehicle_plate']));
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_brand = trim($_POST['vehicle_brand']);
    $vehicle_model = trim($_POST['vehicle_model']);
    $vehicle_year = $_POST['vehicle_year'] ?? null;
    $vehicle_color = trim($_POST['vehicle_color'] ?? '');
    $zone_filter = $_POST['zone_filter'];
    $user_type = $_POST['user_type'];
    
    // Validaciones básicas
    if (!filter_var($owner_email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El email ingresado no es válido");
    }
    
    if (strlen($vehicle_plate) < 5 || strlen($vehicle_plate) > 8) {
        throw new Exception("La patente debe tener entre 5 y 8 caracteres");
    }
    
    if ($vehicle_year && ($vehicle_year < 1990 || $vehicle_year > (date('Y') + 1))) {
        throw new Exception("El año del vehículo no es válido");
    }
    
    // Verificar si ya existe un vehículo con esa patente
    $stmt = $conexion->prepare("SELECT COUNT(*) as count FROM vehiculos WHERE patente = ?");
    $stmt->bind_param("s", $vehicle_plate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        throw new Exception("Ya existe un vehículo registrado con esa patente");
    }
    
    // Crear el nuevo registro de vehículo
    $stmt = $conexion->prepare("
        INSERT INTO vehiculos (
            propietario_nombre, propietario_apellido, propietario_email, propietario_telefono,
            patente, tipo, marca, modelo, año, color, zona_autorizada, tipo_usuario, fecha_registro
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param(
        "ssssssssssss", 
        $owner_first_name, $owner_last_name, $owner_email, $owner_phone,
        $vehicle_plate, $vehicle_type, $vehicle_brand, $vehicle_model, 
        $vehicle_year, $vehicle_color, $zone_filter, $user_type
    );
    
    if ($stmt->execute()) {
        // Redireccionar con mensaje de éxito
        header('Location: ../views/registro_vehiculos_simple.php?mensaje=' . urlencode('Vehículo registrado exitosamente'));
    } else {
        throw new Exception("Error al guardar el vehículo en la base de datos");
    }
    
} catch (Exception $e) {
    // Error - redireccionar con mensaje de error
    header('Location: ../views/registro_vehiculos_simple.php?error=' . urlencode($e->getMessage()));
}

$conexion->close();
exit;
?>
