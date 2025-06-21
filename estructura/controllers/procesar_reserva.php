<?php
// Procesar nueva reserva
session_start();

// Incluir configuración
require_once('../config/config.php');
require_once('../config/conex.php');

try {
    // Verificar que se enviaron todos los datos requeridos
    $campos_requeridos = ['evento', 'fecha', 'hora_inicio', 'hora_fin', 'usuario', 'patente', 'zona'];
    
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
    $zona = $_POST['zona'];
    
    // El tipo de vehículo lo obtenemos de la base de datos basado en la patente
    $tipo_vehiculo = null;
    
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
      // Validar que la patente exista en el sistema y obtener el tipo de vehículo
    $stmt = $pdo->prepare("SELECT tipo FROM vehiculos WHERE patente = ?");
    $stmt->execute([$patente]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado) {
        throw new Exception("La patente '$patente' no está registrada en el sistema. Debe registrar el vehículo primero.");
    }
    
    $tipo_vehiculo = $resultado['tipo'];
    
    // Verificar si ya existe una reserva en esa zona, fecha y horario
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reservas 
        WHERE zona = ? AND fecha = ? 
        AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?))
    ");
    $stmt->execute([$zona, $fecha, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ya existe una reserva en esa zona para el horario solicitado");
    }
    
    // Crear la nueva reserva
    $stmt = $pdo->prepare("
        INSERT INTO reservas (evento, fecha, hora_inicio, hora_fin, usuario, patente, tipo_vehiculo, zona, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([$evento, $fecha, $hora_inicio, $hora_fin, $usuario, $patente, $tipo_vehiculo, $zona]);
    
    if ($result) {
        // Redireccionar con mensaje de éxito
        header('Location: ' . BASE_URL . '/estructura/views/reservas.php?mensaje=' . urlencode('Reserva creada exitosamente'));
    } else {
        throw new Exception("Error al guardar la reserva en la base de datos");
    }
    
} catch (PDOException $e) {
    // Error de base de datos
    header('Location: ' . BASE_URL . '/estructura/views/reservas.php?error=' . urlencode('Error de base de datos: ' . $e->getMessage()));
} catch (Exception $e) {
    // Error general
    header('Location: ' . BASE_URL . '/estructura/views/reservas.php?error=' . urlencode($e->getMessage()));
}

exit;
?>
