<?php
// Procesar nueva reserva usando Dependency Injection
session_start();

// Inicializar la aplicación con DI
require_once dirname(__DIR__) . '/core/Application.php';
require_once dirname(__DIR__) . '/services/ReservaService.php';
require_once dirname(__DIR__) . '/services/ValidationService.php';

try {
    // Inicializar la aplicación
    $app = Application::getInstance();
    
    // Resolver el servicio de reservas desde el container
    $reservaService = $app->get('service.reserva');
    
    // Preparar datos del formulario
    $data = [
        'evento' => $_POST['evento'] ?? '',
        'fecha' => $_POST['fecha'] ?? '',
        'hora_inicio' => $_POST['hora_inicio'] ?? '',
        'hora_fin' => $_POST['hora_fin'] ?? '',
        'usuario' => $_POST['usuario'] ?? '',
        'patente' => strtoupper($_POST['patente'] ?? ''),
        'zona' => $_POST['zona'] ?? ''
    ];
    
    // Usar el servicio para crear la reserva
    $resultado = $reservaService->crearReserva($data);    
    if ($resultado['status'] === 'success') {
        // Redireccionar con mensaje de éxito
        header('Location: ' . BASE_URL . '/estructura/views/reservas.php?mensaje=' . urlencode($resultado['message']));
    } else {
        // Redireccionar con mensaje de error
        header('Location: ' . BASE_URL . '/estructura/views/reservas.php?error=' . urlencode($resultado['message']));
    }
    
} catch (Exception $e) {
    // Error general
    header('Location: ' . BASE_URL . '/estructura/views/reservas.php?error=' . urlencode('Error del sistema: ' . $e->getMessage()));
}

exit;
?>
