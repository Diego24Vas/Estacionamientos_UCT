<?php
// Obtener eventos/reservas
header('Content-Type: application/json');

// Incluir configuración
require_once('../config/config.php');
require_once('../config/conex.php');

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$BD", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultar reservas/eventos
    $stmt = $pdo->prepare("
        SELECT 
            id,
            evento,
            fecha,
            hora_inicio,
            hora_fin,
            zona,
            usuario,
            patente,
            tipo_vehiculo
        FROM reservas 
        WHERE fecha >= CURDATE() 
        ORDER BY fecha ASC, hora_inicio ASC
    ");
    $stmt->execute();
    
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($eventos);
    
} catch (PDOException $e) {
    // En caso de error de base de datos, devolver array vacío
    echo json_encode([]);
} catch (Exception $e) {
    // Error genérico
    echo json_encode(['error' => 'Error obteniendo eventos: ' . $e->getMessage()]);
}
?>
