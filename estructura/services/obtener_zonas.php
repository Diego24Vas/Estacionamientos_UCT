<?php
// Obtener zonas de estacionamiento disponibles
header('Content-Type: application/json');

// Incluir configuración
require_once('../config/config.php');
require_once('../config/conex.php');

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$BD", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultar zonas de estacionamiento
    $stmt = $pdo->prepare("SELECT id, nombre, capacidad FROM zonas_estacionamiento ORDER BY nombre");
    $stmt->execute();
    
    $zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si no hay zonas, crear datos por defecto
    if (empty($zonas)) {
        $zonas = [
            ['id' => 1, 'nombre' => 'Zona A - Estudiantes', 'capacidad' => 50],
            ['id' => 2, 'nombre' => 'Zona B - Profesores', 'capacidad' => 30],
            ['id' => 3, 'nombre' => 'Zona C - Visitantes', 'capacidad' => 20],
            ['id' => 4, 'nombre' => 'Zona D - Administrativos', 'capacidad' => 25]
        ];
    }
    
    echo json_encode($zonas);
    
} catch (PDOException $e) {
    // En caso de error de base de datos, devolver zonas por defecto
    $zonas_default = [
        ['id' => 1, 'nombre' => 'Zona A - Estudiantes', 'capacidad' => 50],
        ['id' => 2, 'nombre' => 'Zona B - Profesores', 'capacidad' => 30],
        ['id' => 3, 'nombre' => 'Zona C - Visitantes', 'capacidad' => 20],
        ['id' => 4, 'nombre' => 'Zona D - Administrativos', 'capacidad' => 25]
    ];
    
    echo json_encode($zonas_default);
} catch (Exception $e) {
    // Error genérico
    echo json_encode(['error' => 'Error obteniendo zonas: ' . $e->getMessage()]);
}
?>
