<?php
/**
 * API REST para Sistema de Estacionamientos UCT
 * 
 * Esta API reemplaza las consultas SQL directas del sistema
 * y proporciona endpoints RESTful para todas las operaciones
 */

// Configuración de CORS y headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Manejo de preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload y configuración
require_once __DIR__ . '/config/DatabaseConnection.php';
require_once __DIR__ . '/config/Router.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/VehicleController.php';
require_once __DIR__ . '/controllers/ReservationController.php';
require_once __DIR__ . '/controllers/SwaggerController.php';

try {
    // Inicializar router
    $router = new Router();
    
    // === RUTAS DE AUTENTICACIÓN ===
    $router->post('/auth/login', [AuthController::class, 'login']);
    $router->post('/auth/register', [AuthController::class, 'register']);
    $router->get('/auth/verify', [AuthController::class, 'verify']);
    
    // === RUTAS DE VEHÍCULOS ===
    $router->get('/vehicles', [VehicleController::class, 'getAll']);
    $router->get('/vehicles/{id}', [VehicleController::class, 'getById']);
    $router->post('/vehicles', [VehicleController::class, 'create']);
    $router->delete('/vehicles/{id}', [VehicleController::class, 'delete']);
    $router->get('/vehicles/validate/{plate}', [VehicleController::class, 'validatePlate']);
    
    // === RUTAS DE RESERVAS ===
    $router->get('/reservations', [ReservationController::class, 'getAll']);
    $router->get('/reservations/{id}', [ReservationController::class, 'getById']);
    $router->post('/reservations', [ReservationController::class, 'create']);
    $router->delete('/reservations/{id}', [ReservationController::class, 'delete']);
    $router->get('/reservations/availability', [ReservationController::class, 'checkAvailability']);
    
    // === RUTAS DE DOCUMENTACIÓN ===
    $router->get('/docs', [SwaggerController::class, 'documentation']);
    $router->get('/swagger.json', [SwaggerController::class, 'getSwaggerJson']);
    
    // === RUTA PRINCIPAL ===
    $router->get('/', function() {
        return [
            'message' => 'API de Estacionamientos UCT',
            'version' => '1.0.0',
            'status' => 'activa',
            'endpoints' => [
                'auth' => [
                    'POST /auth/login' => 'Iniciar sesión',
                    'POST /auth/register' => 'Registrar usuario',
                    'GET /auth/verify' => 'Verificar token'
                ],
                'vehicles' => [
                    'GET /vehicles' => 'Listar vehículos',
                    'GET /vehicles/{id}' => 'Obtener vehículo',
                    'POST /vehicles' => 'Crear vehículo',
                    'DELETE /vehicles/{id}' => 'Eliminar vehículo',
                    'GET /vehicles/validate/{plate}' => 'Validar patente'
                ],
                'reservations' => [
                    'GET /reservations' => 'Listar reservas',
                    'GET /reservations/{id}' => 'Obtener reserva',
                    'POST /reservations' => 'Crear reserva',
                    'DELETE /reservations/{id}' => 'Cancelar reserva',
                    'GET /reservations/availability' => 'Verificar disponibilidad'
                ],
                'documentation' => [
                    'GET /docs' => 'Documentación Swagger',
                    'GET /swagger.json' => 'Especificación OpenAPI'
                ]
            ],
            'documentation_url' => '/api/docs'
        ];
    });
    
    // Procesar la solicitud
    $router->handleRequest();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>