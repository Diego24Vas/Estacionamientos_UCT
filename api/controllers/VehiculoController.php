<?php
require_once './models/Vehiculo.php';

class VehiculoController {
    
    public function listar() {
        try {
            $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 50;
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $offset = ($pagina - 1) * $limite;
            
            $vehiculos = Vehiculo::obtenerTodos($limite, $offset);
            $total = Vehiculo::contarTotal();
            
            echo json_encode([
                'success' => true,
                'data' => $vehiculos,
                'pagination' => [
                    'total' => $total,
                    'pagina' => $pagina,
                    'limite' => $limite,
                    'total_paginas' => ceil($total / $limite)
                ]
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
                echo json_encode(['error' => 'ID del vehículo es requerido']);
                return;
            }
            
            $vehiculo = Vehiculo::obtenerPorId($id);
            
            if (!$vehiculo) {
                http_response_code(404);
                echo json_encode(['error' => 'Vehículo no encontrado']);
                return;
            }
            
            echo json_encode(['success' => true, 'data' => $vehiculo]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function obtenerPorPatente() {
        try {
            $patente = $_GET['patente'] ?? null;
            
            if (!$patente) {
                http_response_code(400);
                echo json_encode(['error' => 'Patente es requerida']);
                return;
            }
            
            $vehiculo = Vehiculo::obtenerPorPatente($patente);
            
            if (!$vehiculo) {
                http_response_code(404);
                echo json_encode(['error' => 'Vehículo no encontrado']);
                return;
            }
            
            echo json_encode(['success' => true, 'data' => $vehiculo]);
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
            $errores = Vehiculo::validarDatos($data);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
                return;
            }
            
            $resultado = Vehiculo::crear($data);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode([
                'success' => true, 
                'mensaje' => 'Vehículo registrado exitosamente',
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
                echo json_encode(['error' => 'ID del vehículo es requerido']);
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
            $errores = Vehiculo::validarDatos($data, true);
            if (!empty($errores)) {
                http_response_code(400);
                echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
                return;
            }
            
            $resultado = Vehiculo::actualizar($id, $data);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Vehículo actualizado exitosamente']);
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
                echo json_encode(['error' => 'ID del vehículo es requerido']);
                return;
            }
            
            $resultado = Vehiculo::eliminar($id);
            
            if (isset($resultado['error'])) {
                http_response_code(400);
                echo json_encode(['error' => $resultado['error']]);
                return;
            }
            
            echo json_encode(['success' => true, 'mensaje' => 'Vehículo eliminado exitosamente']);
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
            
            $vehiculos = Vehiculo::buscar($termino, $limite);
            
            echo json_encode([
                'success' => true,
                'data' => $vehiculos,
                'total' => count($vehiculos)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function listarPorZona() {
        try {
            $zona = $_GET['zona'] ?? null;
            
            if (!$zona) {
                http_response_code(400);
                echo json_encode(['error' => 'Zona es requerida']);
                return;
            }
            
            $vehiculos = Vehiculo::obtenerPorZona($zona);
            
            echo json_encode([
                'success' => true,
                'data' => $vehiculos,
                'total' => count($vehiculos)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}
?>
