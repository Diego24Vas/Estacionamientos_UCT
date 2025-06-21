<?php
session_start();
require_once('../config/config.php');
require_once('../config/conex.php');

header('Content-Type: application/json');

try {
    // Verificar que se envió el ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID de reserva requerido");
    }
    
    $id = intval($_GET['id']);
    
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$BD", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar que la reserva existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->fetchColumn() == 0) {
        throw new Exception("Reserva no encontrada");
    }
    
    // Eliminar la reserva
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Reserva eliminada exitosamente'
        ]);
    } else {
        throw new Exception("Error al eliminar la reserva");
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit;
?>
 