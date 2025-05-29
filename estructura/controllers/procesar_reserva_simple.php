<?php
// Procesar nueva reserva - Versión simplificada como el original
session_start();

// Incluir conexión a la base de datos
require_once('../config/conex.php');

try {    // Verificar que se enviaron todos los datos requeridos
    $campos_requeridos = ['evento', 'fecha', 'hora_inicio', 'hora_fin', 'usuario', 'patente', 'tipoVehiculo', 'zona'];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            throw new Exception("El campo '$campo' es requerido");
        }
    }
    
    // Obtener datos del formulario
    $evento = $_POST['evento'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $usuario = $_POST['usuario'];
    $patente = strtoupper($_POST['patente']);
    $tipo_vehiculo = $_POST['tipoVehiculo'];
    $zona = $_POST['zona'];
    
    // Validaciones básicas
    if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
        throw new Exception("No se puede reservar para fechas pasadas");
    }
    
    if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
        throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
    }
    
    // Verificar si ya existe una reserva en esa zona, fecha y horario
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as count
        FROM reservas 
        WHERE zona = ? AND fecha = ? 
        AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?))
    ");
    $stmt->bind_param("ssssss", $zona, $fecha, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        throw new Exception("Ya existe una reserva en esa zona para el horario solicitado");
    }
      // Crear la nueva reserva
    $stmt = $conexion->prepare("
        INSERT INTO reservas (evento, fecha, hora_inicio, hora_fin, usuario, patente, tipo_vehiculo, zona, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("ssssssss", $evento, $fecha, $hora_inicio, $hora_fin, $usuario, $patente, $tipo_vehiculo, $zona);
    
    if ($stmt->execute()) {
        // Redireccionar con mensaje de éxito
        header('Location: ../views/reservas.php?mensaje=' . urlencode('Reserva creada exitosamente'));
    } else {
        throw new Exception("Error al guardar la reserva en la base de datos");
    }
    
} catch (Exception $e) {
    // Error - redireccionar con mensaje de error
    header('Location: ../views/reservas.php?error=' . urlencode($e->getMessage()));
}

$conexion->close();
exit;
?>
