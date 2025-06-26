<?php
require_once __DIR__ . '/../config/DatabaseConnection.php';

class VehicleController {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }
    
    /**
     * @OA\Get(
     *     path="/vehicles",
     *     summary="Obtener todos los vehículos",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID del usuario para filtrar vehículos",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Lista de vehículos")
     * )
     */
    public function getAll() {
        try {
            $userId = $_GET['user_id'] ?? null;
            
            if ($userId) {
                $vehicles = $this->db->fetchAll(
                    "SELECT v.*, CONCAT(v.nombre, ' ', v.apellido) as propietario 
                     FROM INFO1170_VehiculosRegistrados v 
                     WHERE v.id = ?",
                    [$userId]
                );
            } else {
                $vehicles = $this->db->fetchAll(
                    "SELECT v.*, CONCAT(v.nombre, ' ', v.apellido) as propietario 
                     FROM INFO1170_VehiculosRegistrados v 
                     ORDER BY v.id DESC"
                );
            }
            
            return [
                'success' => true,
                'data' => $vehicles,
                'count' => count($vehicles)
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al obtener vehículos', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Get(
     *     path="/vehicles/{id}",
     *     summary="Obtener vehículo por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Datos del vehículo"),
     *     @OA\Response(response=404, description="Vehículo no encontrado")
     * )
     */
    public function getById($id) {
        try {
            $vehicle = $this->db->fetch(
                "SELECT v.*, CONCAT(v.nombre, ' ', v.apellido) as propietario 
                 FROM INFO1170_VehiculosRegistrados v 
                 WHERE v.id = ?",
                [$id]
            );
            
            if (!$vehicle) {
                http_response_code(404);
                return ['error' => 'Vehículo no encontrado'];
            }
            
            return [
                'success' => true,
                'data' => $vehicle
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al obtener vehículo', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Post(
     *     path="/vehicles",
     *     summary="Crear nuevo vehículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="patente", type="string", example="ABC123"),
     *             @OA\Property(property="nombre", type="string", example="Juan"),
     *             @OA\Property(property="apellido", type="string", example="Pérez"),
     *             @OA\Property(property="espacio_estacionamiento", type="string", example="E1")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Vehículo creado exitosamente"),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['patente', 'nombre', 'apellido'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    return ['error' => "El campo $field es requerido"];
                }
            }
            
            // Verificar si la patente ya existe
            $existingVehicle = $this->db->fetch(
                "SELECT id FROM INFO1170_VehiculosRegistrados WHERE patente = ?",
                [$data['patente']]
            );
            
            if ($existingVehicle) {
                http_response_code(400);
                return ['error' => 'La patente ya está registrada'];
            }
            
            $vehicleId = $this->db->insert(
                "INSERT INTO INFO1170_VehiculosRegistrados (nombre, apellido, patente, espacio_estacionamiento) 
                 VALUES (?, ?, ?, ?)",
                [
                    $data['nombre'],
                    $data['apellido'],
                    $data['patente'],
                    $data['espacio_estacionamiento'] ?? ''
                ]
            );
            
            http_response_code(201);
            return [
                'success' => true,
                'message' => 'Vehículo registrado exitosamente',
                'vehicle_id' => $vehicleId
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al crear vehículo', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Get(
     *     path="/vehicles/validate/{plate}",
     *     summary="Validar patente de vehículo",
     *     @OA\Parameter(
     *         name="plate",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Resultado de validación")
     * )
     */
    public function validatePlate($plate) {
        try {
            $vehicle = $this->db->fetch(
                "SELECT v.*, CONCAT(v.nombre, ' ', v.apellido) as propietario 
                 FROM INFO1170_VehiculosRegistrados v 
                 WHERE v.patente = ?",
                [$plate]
            );
            
            return [
                'success' => true,
                'exists' => $vehicle !== false,
                'vehicle' => $vehicle ?: null
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al validar patente', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Delete(
     *     path="/vehicles/{id}",
     *     summary="Eliminar vehículo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Vehículo eliminado"),
     *     @OA\Response(response=404, description="Vehículo no encontrado")
     * )
     */
    public function delete($id) {
        try {
            $rowsAffected = $this->db->delete(
                "DELETE FROM INFO1170_VehiculosRegistrados WHERE id = ?",
                [$id]
            );
            
            if ($rowsAffected === 0) {
                http_response_code(404);
                return ['error' => 'Vehículo no encontrado'];
            }
            
            return [
                'success' => true,
                'message' => 'Vehículo eliminado exitosamente'
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al eliminar vehículo', 'message' => $e->getMessage()];
        }
    }
}
?>