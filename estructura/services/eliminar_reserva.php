<?php
// Eliminar reserva
header('Content-Type: application/json');

// Incluir configuración
require_once('../config/config.php');
require_once('../config/conex.php');

try {
    // Leer datos JSON del request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        throw new Exception('ID de reserva no proporcionado');
    }
    
    $reserva_id = intval($input['id']);
      // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$BD", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Eliminar la reserva
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
    $result = $stmt->execute([$reserva_id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Reserva eliminada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la reserva']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
