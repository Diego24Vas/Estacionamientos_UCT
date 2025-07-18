<?php
// Procesar registro de vehículo - Con Dependency Injection
require_once dirname(__DIR__) . '/core/Application.php';

try {
    // Inicializar aplicación DI
    $app = Application::getInstance();
    $vehicleService = $app->get('service.vehicle');
    $notificationService = $app->get('service.notification');
    $sessionManager = $app->get('service.session');
      // Verificar que se enviaron datos por POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de solicitud no válido");
    }
    
    // DEBUG: Log de los datos recibidos
    error_log("=== DEBUG VEHICULO ===");
    error_log("POST data: " . print_r($_POST, true));
    
    // Campos requeridos básicos (ajustados al formulario real)
    $campos_requeridos = [
        'owner_first_name' => 'Nombre del propietario',
        'owner_last_name' => 'Apellido del propietario', 
        'vehicle_plate' => 'Patente del vehículo',
        'zone_filter' => 'Zona autorizada'
    ];
    
    // Verificar campos requeridos
    $errores = [];
    foreach ($campos_requeridos as $campo => $nombre) {
        $valor = isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
        error_log("Campo: $campo = '$valor' (empty: " . (empty($valor) ? 'SÍ' : 'NO') . ")");
        
        if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
            $errores[] = "El campo '$nombre' es requerido";
        }
    }
      if (!empty($errores)) {
        error_log("ERRORES ENCONTRADOS: " . implode('. ', $errores));
        throw new Exception(implode('. ', $errores));
    }
    
    // Obtener y limpiar datos del formulario
    $vehicleData = [
        'propietario_nombre' => trim($_POST['owner_first_name']),
        'propietario_apellido' => trim($_POST['owner_last_name']),
        'propietario_email' => trim($_POST['owner_email'] ?? ''),
        'propietario_telefono' => trim($_POST['owner_phone'] ?? ''),
        'patente' => strtoupper(trim($_POST['vehicle_plate'])),
        'tipo' => $_POST['vehicle_type'] ?? 'Auto',
        'marca' => trim($_POST['vehicle_brand'] ?? ''),
        'modelo' => trim($_POST['vehicle_model'] ?? ''),
        'año' => $_POST['vehicle_year'] ?? null,
        'color' => trim($_POST['vehicle_color'] ?? ''),
        'zona_autorizada' => $_POST['zone_filter'],
        'tipo_usuario' => $_POST['user_type'] ?? 'Regular'    ];
    
    // DEBUG: Log de los datos procesados
    error_log("Datos para VehicleService: " . print_r($vehicleData, true));
    
    // Usar VehicleService para crear el vehículo
    $result = $vehicleService->createVehicle($vehicleData);
    
    if ($result['exito']) {
        $notificationService->success($result['mensaje']);
        header('Location: ../views/registro_vehiculos.php?success=1');
    } else {
        throw new Exception($result['mensaje']);
    }
      
} catch (Exception $e) {
    // Error - usar NotificationService y redireccionar
    if (isset($notificationService)) {
        $notificationService->error($e->getMessage());
    }
    
    $errorMsg = urlencode($e->getMessage());
    header('Location: ../views/registro_vehiculos.php?error=' . $errorMsg);
}

exit;
?>
