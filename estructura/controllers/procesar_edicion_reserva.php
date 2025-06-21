<?php
session_start();
require_once('../config/config.php');
require_once('../config/conex.php');

header('Content-Type: application/json');

try {
    // Obtener los datos del POST
    $datos = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que se enviaron todos los datos requeridos
    $campos_requeridos = ['id', 'evento', 'fecha', 'horaInicio', 'horaFin', 'usuario', 'patente', 'zona'];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($datos[$campo]) || empty($datos[$campo])) {
            throw new Exception("El campo '$campo' es requerido");
        }
    }
    
    $id = intval($datos['id']);
    $evento = $datos['evento'];
    $fecha = $datos['fecha'];
    $hora_inicio = $datos['horaInicio'];
    $hora_fin = $datos['horaFin'];
    $usuario = $datos['usuario'];
    $patente = strtoupper($datos['patente']);
    $zona = $datos['zona'];
    
    // Validar que la fecha no sea en el pasado
    if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
        throw new Exception("No se puede reservar para fechas pasadas");
    }
    
    // Validar que la hora de fin sea después de la hora de inicio
    if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
        throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
    }
    
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$BD", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Validar que la patente exista en el sistema
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehiculos WHERE patente = ?");
    $stmt->execute([$patente]);
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("La patente '$patente' no está registrada en el sistema.");
    }
    
    // Verificar que no exista conflicto de horarios (excluyendo la reserva actual)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reservas 
        WHERE zona = ? AND fecha = ? AND id != ?
        AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?))
    ");
    $stmt->execute([$zona, $fecha, $id, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ya existe una reserva en esa zona para el horario solicitado");
    }
    
    // Actualizar la reserva
    $stmt = $pdo->prepare("
        UPDATE reservas 
        SET evento = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, usuario = ?, patente = ?, zona = ?
        WHERE id = ?
    ");
    
    $result = $stmt->execute([$evento, $fecha, $hora_inicio, $hora_fin, $usuario, $patente, $zona, $id]);
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Reserva actualizada exitosamente'
        ]);
    } else {
        throw new Exception("Error al actualizar la reserva");
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit;
?>
