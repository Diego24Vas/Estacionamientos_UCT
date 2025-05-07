<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';

try {
    // Usar el Factory Method para crear la reserva
    $factory = new ReservaFactoryImpl();
    $datosReserva = [
        'id' => $_POST['id'],
        'evento' => $_POST['evento'],
        'fecha' => $_POST['fecha'],
        'horaInicio' => $_POST['horaInicio'],
        'horaFin' => $_POST['horaFin'],
        'zona' => $_POST['zona']
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
