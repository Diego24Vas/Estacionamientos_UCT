<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once dirname(__DIR__) . '/core/Application.php';

try {
    // Inicializar aplicación DI
    $app = Application::getInstance();
    $parkingService = $app->get('service.parking');
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetRequest($parkingService);
            break;
            
        case 'POST':
            handlePostRequest($parkingService);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}

function handleGetRequest($parkingService) {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'availability':
            checkAvailability($parkingService);
            break;
            
        case 'stats':
            getOccupancyStats($parkingService);
            break;
            
        case 'config':
            getConfiguration($parkingService);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

function handlePostRequest($parkingService) {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'release':
            releaseSpace($parkingService, $input);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

function checkAvailability($parkingService) {
    $zona = $_GET['zona'] ?? '';
    $fecha = $_GET['fecha'] ?? '';
    $horaInicio = $_GET['hora_inicio'] ?? '';
    $horaFin = $_GET['hora_fin'] ?? '';
    
    if (empty($zona) || empty($fecha) || empty($horaInicio) || empty($horaFin)) {
        http_response_code(400);
        echo json_encode(['error' => 'Parámetros requeridos: zona, fecha, hora_inicio, hora_fin']);
        return;
    }
    
    $availability = $parkingService->checkAvailability($zona, $fecha, $horaInicio, $horaFin);
    echo json_encode($availability);
}

function getOccupancyStats($parkingService) {
    $fecha = $_GET['fecha'] ?? null;
    $stats = $parkingService->getOccupancyStats($fecha);
    echo json_encode($stats);
}

function getConfiguration($parkingService) {
    $config = $parkingService->getMaxSpacesConfiguration();
    echo json_encode([
        'maxSpacesByZone' => $config,
        'zones' => [
            'A' => 'Zona A - Administrativa',
            'B' => 'Zona B - Académica',
            'C' => 'Zona C - Deportiva',
            'D' => 'Zona D - Visitantes'
        ]
    ]);
}

function releaseSpace($parkingService, $data) {
    $reservationId = $data['reservationId'] ?? null;
    
    if (!$reservationId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de reserva requerido']);
        return;
    }
    
    $result = $parkingService->releaseSpace($reservationId);
    
    if ($result['success']) {
        echo json_encode($result);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
}
?>
