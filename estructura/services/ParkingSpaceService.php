<?php

/**
 * Parking Space Management Service
 * Servicio para gestión de cupos 
 */
class ParkingSpaceService {
    private $connection;
    private $validationService;
    private $notificationService;
    
    // Configuración de cupos máximos por zona
    private $maxSpacesByZone = [
        'A' => 50,  // Zona A - Administrativa
        'B' => 75,  // Zona B - Académica  
        'C' => 30,  // Zona C - Deportiva
        'D' => 25   // Zona D - Visitantes
    ];
    
    public function __construct($connection, ValidationService $validationService, NotificationService $notificationService) {
        $this->connection = $connection;
        $this->validationService = $validationService;
        $this->notificationService = $notificationService;
    }
    
    /**
     * Verificar disponibilidad de espacios en una zona para un horario específico
     */
    public function checkAvailability(string $zona, string $fecha, string $horaInicio, string $horaFin): array {
        try {
            // Obtener cupo máximo para la zona
            $maxSpaces = $this->getMaxSpacesForZone($zona);
            
            // Contar reservas activas en el mismo horario y zona
            $occupiedSpaces = $this->countOccupiedSpaces($zona, $fecha, $horaInicio, $horaFin);
            
            // Calcular espacios disponibles
            $availableSpaces = $maxSpaces - $occupiedSpaces;
            
            return [
                'available' => $availableSpaces > 0,
                'availableSpaces' => max(0, $availableSpaces),
                'maxSpaces' => $maxSpaces,
                'occupiedSpaces' => $occupiedSpaces,
                'utilizationPercentage' => round(($occupiedSpaces / $maxSpaces) * 100, 2)
            ];
        } catch (Exception $e) {
            error_log("Error checking availability: " . $e->getMessage());
            return [
                'available' => false,
                'error' => 'Error al verificar disponibilidad: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Reservar un espacio si está disponible
     */
    public function reserveSpace(array $reservationData): array {
        try {
            $zona = $reservationData['zona'];
            $fecha = $reservationData['fecha'];
            $horaInicio = $reservationData['hora_inicio'];
            $horaFin = $reservationData['hora_fin'];
            
            // Verificar disponibilidad
            $availability = $this->checkAvailability($zona, $fecha, $horaInicio, $horaFin);
            
            if (!$availability['available']) {
                return [
                    'success' => false,
                    'message' => 'No hay espacios disponibles en la zona ' . $zona . ' para el horario solicitado.',
                    'availability' => $availability
                ];
            }
            
            // Buscar el siguiente número de espacio disponible
            $spaceNumber = $this->getNextAvailableSpaceNumber($zona);
            
            // Crear la reserva
            $reservationResult = $this->createReservation($reservationData, $spaceNumber);
            
            if ($reservationResult['success']) {
                // Verificar si la zona está cerca del límite
                $this->checkAndNotifyCapacityWarnings($zona, $fecha, $horaInicio, $horaFin);
                
                return [
                    'success' => true,
                    'message' => 'Reserva creada exitosamente. Espacio asignado: ' . $spaceNumber,
                    'spaceNumber' => $spaceNumber,
                    'availability' => $this->checkAvailability($zona, $fecha, $horaInicio, $horaFin)
                ];
            } else {
                return $reservationResult;
            }
            
        } catch (Exception $e) {
            error_log("Error reserving space: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la reserva: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Liberar un espacio
     */
    public function releaseSpace(int $reservationId): array {
        try {
            // Obtener información de la reserva
            $reservation = $this->getReservationById($reservationId);
            
            if (!$reservation) {
                return [
                    'success' => false,
                    'message' => 'Reserva no encontrada'
                ];
            }
            
            // Marcar como completada/liberada
            $stmt = $this->connection->prepare("
                UPDATE INFO1170_Reservas 
                SET estado = 'completada', fecha_liberacion = NOW() 
                WHERE id = ?
            ");
            $stmt->bind_param("i", $reservationId);
            
            if ($stmt->execute()) {
                $stmt->close();
                
                // Notificar liberación de espacio
                $this->notifySpaceReleased($reservation);
                
                return [
                    'success' => true,
                    'message' => 'Espacio liberado exitosamente',
                    'reservation' => $reservation
                ];
            } else {
                $stmt->close();
                return [
                    'success' => false,
                    'message' => 'Error al liberar el espacio'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error releasing space: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al liberar espacio: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener estadísticas de ocupación por zona
     */
    public function getOccupancyStats(string $fecha = null): array {
        $fecha = $fecha ?: date('Y-m-d');
        $stats = [];
        
        foreach ($this->maxSpacesByZone as $zona => $maxSpaces) {
            $occupied = $this->countOccupiedSpacesToday($zona, $fecha);
            $stats[$zona] = [
                'zona' => $zona,
                'maxSpaces' => $maxSpaces,
                'occupiedSpaces' => $occupied,
                'availableSpaces' => max(0, $maxSpaces - $occupied),
                'utilizationPercentage' => round(($occupied / $maxSpaces) * 100, 2),
                'status' => $this->getZoneStatus($occupied, $maxSpaces)
            ];
        }
        
        return $stats;
    }
    
    /**
     * Contar espacios ocupados en una zona para un horario específico
     */
    private function countOccupiedSpaces(string $zona, string $fecha, string $horaInicio, string $horaFin): int {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as count 
            FROM INFO1170_Reservas r
            WHERE r.zona = ? 
            AND r.fecha = ?
            AND r.estado IN ('activa', 'confirmada')
            AND (
                (r.hora_inicio <= ? AND r.hora_fin > ?) OR
                (r.hora_inicio < ? AND r.hora_fin >= ?) OR
                (r.hora_inicio >= ? AND r.hora_fin <= ?)
            )
        ");
        
        $stmt->bind_param("ssssssss", 
            $zona, $fecha, 
            $horaInicio, $horaInicio,
            $horaFin, $horaFin,
            $horaInicio, $horaFin
        );
        
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();
        
        return (int)$count;
    }
    
    /**
     * Contar espacios ocupados hoy en una zona
     */
    private function countOccupiedSpacesToday(string $zona, string $fecha): int {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as count 
            FROM INFO1170_Reservas 
            WHERE zona = ? AND fecha = ? AND estado IN ('activa', 'confirmada')
        ");
        
        $stmt->bind_param("ss", $zona, $fecha);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();
        
        return (int)$count;
    }
    
    /**
     * Obtener cupo máximo para una zona
     */
    private function getMaxSpacesForZone(string $zona): int {
        return $this->maxSpacesByZone[$zona] ?? 0;
    }
    
    /**
     * Obtener siguiente número de espacio disponible
     */
    private function getNextAvailableSpaceNumber(string $zona): string {
        $maxSpaces = $this->getMaxSpacesForZone($zona);
        
        for ($i = 1; $i <= $maxSpaces; $i++) {
            $spaceNumber = $zona . sprintf('%03d', $i); // A001, B001, etc.
            
            // Verificar si este espacio está disponible
            if ($this->isSpaceNumberAvailable($spaceNumber)) {
                return $spaceNumber;
            }
        }
        
        // Si no hay espacios numerados disponibles, generar uno temporal
        return $zona . '_' . uniqid();
    }
    
    /**
     * Verificar si un número de espacio está disponible
     */
    private function isSpaceNumberAvailable(string $spaceNumber): bool {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as count 
            FROM INFO1170_Reservas 
            WHERE numero_espacio = ? AND estado IN ('activa', 'confirmada')
        ");
        
        $stmt->bind_param("s", $spaceNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        $stmt->close();
        
        return $count == 0;
    }
    
    /**
     * Crear reserva en la base de datos
     */
    private function createReservation(array $data, string $spaceNumber): array {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO INFO1170_Reservas 
                (evento, fecha, zona, hora_inicio, hora_fin, usuario, patente, numero_espacio, estado, fecha_creacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activa', NOW())
            ");
            
            $stmt->bind_param("ssssssss",
                $data['evento'],
                $data['fecha'],
                $data['zona'],
                $data['hora_inicio'],
                $data['hora_fin'],
                $data['usuario'],
                $data['patente'],
                $spaceNumber
            );
            
            if ($stmt->execute()) {
                $reservationId = $this->connection->insert_id;
                $stmt->close();
                
                return [
                    'success' => true,
                    'reservationId' => $reservationId,
                    'spaceNumber' => $spaceNumber
                ];
            } else {
                $error = $stmt->error;
                $stmt->close();
                return [
                    'success' => false,
                    'message' => 'Error al crear la reserva: ' . $error
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear reserva: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verificar y notificar sobre advertencias de capacidad
     */
    private function checkAndNotifyCapacityWarnings(string $zona, string $fecha, string $horaInicio, string $horaFin): void {
        $availability = $this->checkAvailability($zona, $fecha, $horaInicio, $horaFin);
        $utilizationPercentage = $availability['utilizationPercentage'];
        
        if ($utilizationPercentage >= 90) {
            $this->notificationService->warning(
                "La zona {$zona} está al {$utilizationPercentage}% de su capacidad máxima."
            );
        } elseif ($utilizationPercentage >= 95) {
            $this->notificationService->error(
                "¡ATENCIÓN! La zona {$zona} está al {$utilizationPercentage}% de su capacidad. Solo quedan {$availability['availableSpaces']} espacios."
            );
        }
    }
    
    /**
     * Obtener información de una reserva
     */
    private function getReservationById(int $id): ?array {
        $stmt = $this->connection->prepare("
            SELECT * FROM INFO1170_Reservas WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reservation = $result->fetch_assoc();
        $stmt->close();
        
        return $reservation ?: null;
    }
    
    /**
     * Notificar liberación de espacio
     */
    private function notifySpaceReleased(array $reservation): void {
        $this->notificationService->success(
            "Espacio {$reservation['numero_espacio']} liberado en zona {$reservation['zona']}."
        );
    }
    
    /**
     * Obtener estado de la zona según ocupación
     */
    private function getZoneStatus(int $occupied, int $max): string {
        $percentage = ($occupied / $max) * 100;
        
        if ($percentage >= 95) return 'critical';
        if ($percentage >= 80) return 'warning';
        if ($percentage >= 60) return 'busy';
        return 'available';
    }
    
    /**
     * Configurar cupos máximos por zona
     */
    public function setMaxSpacesForZone(string $zona, int $maxSpaces): void {
        $this->maxSpacesByZone[$zona] = $maxSpaces;
    }
    
    /**
     * Obtener configuración de cupos
     */
    public function getMaxSpacesConfiguration(): array {
        return $this->maxSpacesByZone;
    }
}
