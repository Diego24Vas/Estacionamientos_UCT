<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';
include('conex.php'); // Asegúrate de tener la conexión a la base de datos disponible

// Clase observadora para notificaciones por correo
class EmailNotifier implements ReservaObserver {
    public function update($message) {
        // Aquí iría la lógica para enviar correos
        error_log("Notificación por correo: " . $message);
    }
}

// Clase observadora para notificaciones en la interfaz
class UINotifier implements ReservaObserver {
    public function update($message) {
        // Aquí iría la lógica para mostrar notificaciones en la UI
        $_SESSION['notificacion'] = $message;
    }
}

// Clase observadora para registro de auditoría
class AuditNotifier implements ReservaObserver {
    public function update($message) {
        // Aquí iría la lógica para registrar en el log de auditoría
        error_log("Auditoría: " . $message);
    }
}

try {
    // Usar el Factory Method para crear la reserva
    $factory = new ReservaFactoryImpl();
    
    // Preparar los datos para la reserva
    $datosReserva = [
        'evento' => $_POST['evento'],
        'fecha' => $_POST['fecha'],
        'horaInicio' => $_POST['horaInicio'],
        'horaFin' => $_POST['horaFin'],
        'zona' => $_POST['zona']
    ];
    
    // Crear la reserva usando el Factory
    $reserva = $factory->crearReserva($datosReserva);
    
    // Agregar los observadores
    $reserva->attachObserver(new EmailNotifier());
    $reserva->attachObserver(new UINotifier());
    $reserva->attachObserver(new AuditNotifier());
    
    // Intentar crear la reserva (usando la interfaz IReserva)
    if ($reserva->crearReserva()) {
        header('Location: ../views/reservas.php?mensaje=Reserva creada exitosamente');
    } else {
        header('Location: ../views/reservas.php?error=Error al crear la reserva');
    }
} catch (Exception $e) {
    header('Location: ../views/reservas.php?error=' . urlencode($e->getMessage()));
}
?>
