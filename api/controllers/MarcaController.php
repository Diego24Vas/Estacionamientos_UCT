<?php
require_once './models/Marca.php';

class MarcaController {
    
    public function listar() {
        try {
            $soloActivas = isset($_GET['activas']) ? (bool)$_GET['activas'] : true;
            $conEstadisticas = isset($_GET['estadisticas']) ? (bool)$_GET['estadisticas'] : false;
            
            if ($conEstadisticas) {
                $marcas = Marca::obtenerConEstadisticas();
            } else {
                $marcas = Marca::obtenerTodas($soloActivas);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $marcas,
                'total' => count($marcas)
            ]);
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
                echo json_encode(['error' => 'ID de marca es requerido']);
                return;
            }
            
            $marca = Marca::obtenerPorId($id);
            
            if (!$marca) {
                http_response_code(404);
                echo json_encode(['error' => 'Marca no encontrada']);
                return;
            }
            
            echo json_encode(['success' => true, 'data' => $marca]);
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
            $errores = Marca::validarDatos($data);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
                return;
            }
            
            $resultado = Marca::crear($data);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode([
                'success' => true, 
                'mensaje' => 'Marca creada exitosamente',
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
                echo json_encode(['error' => 'ID de marca es requerido']);
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
            $errores = Marca::validarDatos($data, true);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
                return;
            }
            
            $resultado = Marca::actualizar($id, $data);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Marca actualizada exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function eliminar() {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de marca es requerido']);
                return;
            }
            
            $resultado = Marca::eliminar($id);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Marca eliminada exitosamente']);
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
                echo json_encode(['error' => 'ID de marca es requerido']);
                return;
            }
            
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            if (!isset($data['activa'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Estado activa es requerido']);
                return;
            }
            
            $resultado = Marca::cambiarEstado($id, $data['activa']);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Estado de marca actualizado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function buscar() {
        try {
            $termino = $_GET['q'] ?? '';
            $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 20;
            
            if (strlen($termino) < 2) {
                http_response_code(400);
                echo json_encode(['error' => 'El término de búsqueda debe tener al menos 2 caracteres']);
                return;
            }
            
            $marcas = Marca::buscar($termino, $limite);
            
            echo json_encode([
                'success' => true,
                'data' => $marcas,
                'total' => count($marcas)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function masUsadas() {
        try {
            $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
            
            $marcas = Marca::obtenerMasUsadas($limite);
            
            echo json_encode([
                'success' => true,
                'data' => $marcas,
                'total' => count($marcas)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function importarComunes() {
        try {
            $resultado = Marca::importarMarcasComunes();
            
            echo json_encode([
                'success' => true,
                'mensaje' => 'Marcas comunes importadas exitosamente',
                'marcas_creadas' => $resultado['marcas_creadas']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}
?>
