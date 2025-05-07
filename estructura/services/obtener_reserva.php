<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';

try {
    // Usar el Factory Method para crear la reserva
    $factory = new ReservaFactoryImpl();
    $datosReserva = ['id' => $_GET['id']];
    $reserva = $factory->crearReserva($datosReserva);
    
    // Obtener la reserva usando la interfaz IReserva
    $reserva_data = $reserva->obtenerReservaPorId();
    
    header('Content-Type: application/json');
    echo json_encode($reserva_data);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>

