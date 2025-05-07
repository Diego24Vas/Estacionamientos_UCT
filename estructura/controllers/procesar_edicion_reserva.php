<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';

// Clase observadora para notificaciones por correo
class EmailNotifier implements ReservaObserver {
    public function update($message) {
        error_log("Notificación por correo: " . $message);
    }
}

// Clase observadora para notificaciones en la interfaz
class UINotifier implements ReservaObserver {
    public function update($message) {
        $_SESSION['notificacion'] = $message;
    }
}

// Clase observadora para registro de auditoría
class AuditNotifier implements ReservaObserver {
    public function update($message) {
        error_log("Auditoría: " . $message);
    }
}

try {
    // Obtener los datos del POST
    $datos = json_decode(file_get_contents('php://input'), true);
    
    // Usar el Factory Method para crear la reserva
    $factory = new ReservaFactoryImpl();
    $datosReserva = [
        'id' => $datos['id'],
        'evento' => $datos['evento'],
        'fecha' => $datos['fecha'],
        'horaInicio' => $datos['horaInicio'],
        'horaFin' => $datos['horaFin'],
        'zona' => $datos['zona'],
        'tipoVehiculo' => $datos['tipoVehiculo'],
        'usuarioId' => $_SESSION['usuario_id'] ?? null,
        'capacidadMaxima' => $datos['capacidadMaxima'] ?? 1
    ];
    
    $reserva = $factory->crearReserva($datosReserva);
    
    // Agregar observadores
    $reserva->attachObserver(new EmailNotifier());
    $reserva->attachObserver(new UINotifier());
    $reserva->attachObserver(new AuditNotifier());
    
    // Actualizar la reserva usando la interfaz IReserva
    if ($reserva->actualizarReserva()) {
        echo json_encode(['status' => 'success', 'message' => 'Reserva actualizada correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la reserva.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
