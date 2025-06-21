<?php
// Validar patente usando Dependency Injection
require_once dirname(__DIR__) . '/core/Application.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['patente']) || empty($_GET['patente'])) {
        throw new Exception("Patente requerida");
    }
    
    $patente = strtoupper(trim($_GET['patente']));
    
    // Inicializar la aplicación y obtener la conexión de la BD
    $app = Application::getInstance();
    $database = $app->get('database');
    
    // Verificar si la patente existe
    $stmt = $database->prepare("SELECT COUNT(*) FROM vehiculos WHERE patente = ?");
    $stmt->execute([$patente]);
    $existe = $stmt->fetchColumn() > 0;
    
    echo json_encode([
        'existe' => $existe,
        'patente' => $patente
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}

exit;
?>
