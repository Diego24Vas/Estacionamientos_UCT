<?php
require_once './models/Zona.php';

class ZonaController {
    
    public function listar() {
        try {
            $soloActivas = isset($_GET['activas']) ? (bool)$_GET['activas'] : true;
            $zonas = Zona::obtenerTodas($soloActivas);
            
            echo json_encode([
                'success' => true,
                'data' => $zonas,
                'total' => count($zonas)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function obtenerPorCodigo() {
        try {
            $zona = $_GET['zona'] ?? null;
            
            if (!$zona) {
                http_response_code(400);
                echo json_encode(['error' => 'Código de zona es requerido']);
                return;
            }
            
            $zonaData = Zona::obtenerPorCodigo($zona);
            
            if (!$zonaData) {
                http_response_code(404);
                echo json_encode(['error' => 'Zona no encontrada']);
                return;
            }
            
            echo json_encode(['success' => true, 'data' => $zonaData]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function obtenerPorId() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de zona es requerido']);
                return;
            }
            
            $zona = Zona::obtenerPorId($id);
            
            if (!$zona) {
                http_response_code(404);
                echo json_encode(['error' => 'Zona no encontrada']);
                return;
            }
            
            echo json_encode(['success' => true, 'data' => $zona]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function crear() {
        try {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos JSON inválidos']);
                return;
            }
            
            // Validar datos
            $errores = Zona::validarDatos($data);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
                return;
            }
            
            $resultado = Zona::crear($data);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode([
                'success' => true, 
                'mensaje' => 'Zona creada exitosamente',
                'id' => $resultado['id']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function actualizar() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de zona es requerido']);
                return;
            }
            
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos JSON inválidos']);
                return;
            }
            
            // Validar datos
            $errores = Zona::validarDatos($data, true);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
                return;
            }
            
            $resultado = Zona::actualizar($id, $data);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Zona actualizada exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function cambiarEstado() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de zona es requerido']);
                return;
            }
            
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            if (!isset($data['activa'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Estado activa es requerido']);
                return;
            }
            
            $resultado = Zona::cambiarEstado($id, $data['activa']);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Estado de zona actualizado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function disponibilidad() {
        try {
            $zona = $_GET['zona'] ?? null;
            
            if ($zona) {
                $disponibilidad = Zona::obtenerDisponibilidad($zona);
                if (!$disponibilidad) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Zona no encontrada']);
                    return;
                }
            } else {
                $disponibilidad = Zona::obtenerDisponibilidad();
            }
            
            echo json_encode([
                'success' => true,
                'data' => $disponibilidad
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function estadisticas() {
        try {
            $estadisticas = Zona::obtenerEstadisticas();
            
            echo json_encode([
                'success' => true,
                'data' => $estadisticas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function ocupar() {
        try {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            if (!isset($data['zona'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Zona es requerida']);
                return;
            }
            
            $incremento = $data['espacios'] ?? 1;
            
            $resultado = Zona::actualizarCupoReservado($data['zona'], $incremento);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Espacio ocupado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function liberar() {
        try {
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            if (!isset($data['zona'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Zona es requerida']);
                return;
            }
            
            $decremento = ($data['espacios'] ?? 1) * -1;
            
            $resultado = Zona::actualizarCupoReservado($data['zona'], $decremento);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Espacio liberado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}
?>
