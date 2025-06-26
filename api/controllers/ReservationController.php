<?php
require_once __DIR__ . '/../config/DatabaseConnection.php';

class ReservationController {
    private $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }
    
    /**
     * @OA\Get(
     *     path="/reservations",
     *     summary="Obtener todas las reservas",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="ID del usuario para filtrar reservas",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fecha",
     *         in="query",
     *         description="Fecha específica (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Lista de reservas")
     * )
     */
    public function getAll() {
        try {
            $userId = $_GET['user_id'] ?? null;
            $fecha = $_GET['fecha'] ?? null;
            
            $sql = "SELECT r.*, u.nombre as usuario_nombre 
                    FROM INFO1170_Reservas r 
                    LEFT JOIN INFO1170_RegistroUsuarios u ON r.usuario_id = u.id";
            
            $params = [];
            $conditions = [];
            
            if ($userId) {
                $conditions[] = "r.usuario_id = ?";
                $params[] = $userId;
            }
            
            if ($fecha) {
                $conditions[] = "DATE(r.fecha) = ?";
                $params[] = $fecha;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY r.fecha DESC, r.hora_inicio DESC";
            
            $reservations = $this->db->fetchAll($sql, $params);
            
            return [
                'success' => true,
                'data' => $reservations,
                'count' => count($reservations)
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al obtener reservas', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Post(
     *     path="/reservations",
     *     summary="Crear nueva reserva",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="evento", type="string", example="Reunión de trabajo"),
     *             @OA\Property(property="fecha", type="string", format="date", example="2025-06-25"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="08:00"),
     *             @OA\Property(property="hora_fin", type="string", format="time", example="18:00"),
     *             @OA\Property(property="zona", type="string", example="A"),
     *             @OA\Property(property="usuario", type="string", example="Juan Pérez"),
     *             @OA\Property(property="patente", type="string", example="ABC123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Reserva creada exitosamente"),
     *     @OA\Response(response=400, description="Datos inválidos"),
     *     @OA\Response(response=409, description="Conflicto de horarios")
     * )
     */
    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['evento', 'fecha', 'hora_inicio', 'hora_fin', 'zona', 'usuario', 'patente'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    return ['error' => "El campo $field es requerido"];
                }
            }
            
            // Verificar disponibilidad del espacio
            $conflict = $this->db->fetch(
                "SELECT id FROM INFO1170_Reservas 
                 WHERE zona = ? 
                 AND fecha = ?
                 AND (
                     (hora_inicio <= ? AND hora_fin > ?) OR
                     (hora_inicio < ? AND hora_fin >= ?) OR
                     (hora_inicio >= ? AND hora_fin <= ?)
                 )",
                [
                    $data['zona'],
                    $data['fecha'],
                    $data['hora_inicio'], $data['hora_inicio'],
                    $data['hora_fin'], $data['hora_fin'],
                    $data['hora_inicio'], $data['hora_fin']
                ]
            );
            
            if ($conflict) {
                http_response_code(409);
                return ['error' => 'Ya existe una reserva en esa zona para el horario solicitado'];
            }
            
            // Verificar que la patente existe en el sistema
            $vehicle = $this->db->fetch(
                "SELECT id FROM INFO1170_VehiculosRegistrados WHERE patente = ?",
                [$data['patente']]
            );
            
            if (!$vehicle) {
                http_response_code(400);
                return ['error' => 'La patente no está registrada en el sistema'];
            }
            
            $reservationId = $this->db->insert(
                "INSERT INTO INFO1170_Reservas (evento, fecha, hora_inicio, hora_fin, zona, usuario, patente, usuario_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $data['evento'],
                    $data['fecha'],
                    $data['hora_inicio'],
                    $data['hora_fin'],
                    $data['zona'],
                    $data['usuario'],
                    $data['patente'],
                    $data['usuario_id'] ?? 1
                ]
            );
            
            http_response_code(201);
            return [
                'success' => true,
                'message' => 'Reserva creada exitosamente',
                'reservation_id' => $reservationId
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al crear reserva', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Get(
     *     path="/reservations/availability",
     *     summary="Verificar disponibilidad de espacios",
     *     @OA\Parameter(
     *         name="zona",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fecha",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="hora_inicio",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="hora_fin",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Response(response=200, description="Información de disponibilidad")
     * )
     */
    public function checkAvailability() {
        try {
            $zona = $_GET['zona'] ?? null;
            $fecha = $_GET['fecha'] ?? null;
            $horaInicio = $_GET['hora_inicio'] ?? null;
            $horaFin = $_GET['hora_fin'] ?? null;
            
            if (!$zona || !$fecha || !$horaInicio || !$horaFin) {
                http_response_code(400);
                return ['error' => 'zona, fecha, hora_inicio y hora_fin son requeridas'];
            }
            
            // Obtener capacidad total de la zona desde INFO1170_Estacionamiento
            $totalSpaces = $this->db->fetch(
                "SELECT COUNT(*) as total FROM INFO1170_Estacionamiento WHERE Ubicacion LIKE ?",
                ['Zona ' . strtoupper($zona)]
            );
            
            $maxSpaces = $totalSpaces['total'] ?? 10;
            
            // Contar reservas ocupadas en el horario
            $occupiedSpaces = $this->db->fetch(
                "SELECT COUNT(*) as count FROM INFO1170_Reservas 
                 WHERE zona = ? 
                 AND fecha = ?
                 AND (
                     (hora_inicio <= ? AND hora_fin > ?) OR
                     (hora_inicio < ? AND hora_fin >= ?) OR
                     (hora_inicio >= ? AND hora_fin <= ?)
                 )",
                [
                    $zona, $fecha,
                    $horaInicio, $horaInicio,
                    $horaFin, $horaFin,
                    $horaInicio, $horaFin
                ]
            );
            
            $occupied = $occupiedSpaces['count'] ?? 0;
            $available = max(0, $maxSpaces - $occupied);
            
            return [
                'success' => true,
                'available' => $available > 0,
                'available_spaces' => $available,
                'max_spaces' => $maxSpaces,
                'occupied_spaces' => $occupied,
                'utilization_percentage' => $maxSpaces > 0 ? round(($occupied / $maxSpaces) * 100, 2) : 0
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al verificar disponibilidad', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Get(
     *     path="/reservations/{id}",
     *     summary="Obtener reserva por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Datos de la reserva"),
     *     @OA\Response(response=404, description="Reserva no encontrada")
     * )
     */
    public function getById($id) {
        try {
            $reservation = $this->db->fetch(
                "SELECT r.*, u.nombre as usuario_nombre 
                 FROM INFO1170_Reservas r 
                 LEFT JOIN INFO1170_RegistroUsuarios u ON r.usuario_id = u.id 
                 WHERE r.id = ?",
                [$id]
            );
            
            if (!$reservation) {
                http_response_code(404);
                return ['error' => 'Reserva no encontrada'];
            }
            
            return [
                'success' => true,
                'data' => $reservation
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al obtener reserva', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * @OA\Delete(
     *     path="/reservations/{id}",
     *     summary="Eliminar reserva",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Reserva eliminada"),
     *     @OA\Response(response=404, description="Reserva no encontrada")
     * )
     */
    public function delete($id) {
        try {
            $rowsAffected = $this->db->delete(
                "DELETE FROM INFO1170_Reservas WHERE id = ?",
                [$id]
            );
            
            if ($rowsAffected === 0) {
                http_response_code(404);
                return ['error' => 'Reserva no encontrada'];
            }
            
            return [
                'success' => true,
                'message' => 'Reserva eliminada exitosamente'
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Error al eliminar reserva', 'message' => $e->getMessage()];
        }
    }
}
?>