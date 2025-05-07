<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';

try {
    // Usar el Factory Method para crear la reserva
    $factory = new ReservaFactoryImpl();
    $datosReserva = ['id' => $_GET['id']];
    $reserva = $factory->crearReserva($datosReserva);
    
    // Agregar observadores
    $reserva->attachObserver(new EmailNotifier());
    $reserva->attachObserver(new UINotifier());
    $reserva->attachObserver(new AuditNotifier());
    
    // Eliminar la reserva usando la interfaz IReserva
    if ($reserva->eliminarReserva()) {
        echo json_encode(['status' => 'success', 'message' => 'Reserva eliminada con éxito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar la reserva.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
 