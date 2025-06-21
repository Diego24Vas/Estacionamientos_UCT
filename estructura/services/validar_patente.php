<?php
require_once '../config/config.php';
require_once '../config/conex.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['patente']) || empty($_GET['patente'])) {
        throw new Exception("Patente requerida");
    }
    
    $patente = strtoupper(trim($_GET['patente']));
    
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$BD", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si la patente existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehiculos WHERE patente = ?");
    $stmt->execute([$patente]);
    $existe = $stmt->fetchColumn() > 0;
    
    echo json_encode([
        'existe' => $existe,
        'patente' => $patente
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}

exit;
?>
