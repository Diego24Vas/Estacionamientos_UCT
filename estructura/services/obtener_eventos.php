<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';

try {
    // Usar el Factory Method para crear la reserva
    $factory = new ReservaFactoryImpl();
    $reserva = $factory->crearReserva([]);
    
    // Obtener todas las reservas usando la interfaz IReserva
    $eventos = $reserva->obtenerTodasLasReservas();
    
    header('Content-Type: application/json');
    echo json_encode($eventos);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>
