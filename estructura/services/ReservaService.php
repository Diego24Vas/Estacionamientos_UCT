<?php

require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';

/**
 * Servicio para manejo de reservas con DI
 */
class ReservaService {
    private $reservaRepository;
    private $vehicleRepository;
    private $validationService;
    private $notificationService;
    
    public function __construct(
        $reservaRepository = null,
        $vehicleRepository = null, 
        $validationService = null,
        $notificationService = null
    ) {
        // Si no se inyectan, resolver desde el container
        $this->reservaRepository = $reservaRepository ?? app('repository.reserva');
        $this->vehicleRepository = $vehicleRepository ?? app('repository.vehicle');
        $this->validationService = $validationService ?? app('service.validation');
        $this->notificationService = $notificationService ?? app('service.notification');
    }
    
    /**
     * Crear una nueva reserva
     */
    public function crearReserva(array $data): array {
        try {
            // Validar datos de entrada
            $validation = $this->validationService->validateReserva($data);
            if (!$validation['valid']) {
                return ['status' => 'error', 'message' => implode(', ', $validation['errors'])];
            }
            
            // Validar que la patente existe
            $vehicleExists = $this->vehicleRepository->existsByPatente($data['patente']);
            if (!$vehicleExists) {
                return [
                    'status' => 'error', 
                    'message' => "La patente '{$data['patente']}' no está registrada en el sistema."
                ];
            }
            
            // Verificar disponibilidad de la zona
            $disponible = $this->reservaRepository->verificarDisponibilidad(
                $data['zona'], 
                $data['fecha'], 
                $data['hora_inicio'], 
                $data['hora_fin']
            );
            
            if (!$disponible) {
                return [
                    'status' => 'error',
                    'message' => 'Ya existe una reserva en esa zona para el horario solicitado'
                ];
            }
            
            // Crear la reserva
            $reservaId = $this->reservaRepository->crear($data);
            
            if ($reservaId) {
                // Enviar notificaciones
                $this->notificationService->notificarReservaCreada($data);
                
                return [
                    'status' => 'success',
                    'message' => 'Reserva creada exitosamente',
                    'reserva_id' => $reservaId
                ];
            } else {
                return ['status' => 'error', 'message' => 'Error al crear la reserva'];
            }
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Actualizar una reserva existente
     */
    public function actualizarReserva(int $id, array $data): array {
        try {
            // Validar datos
            $validation = $this->validationService->validateReserva($data);
            if (!$validation['valid']) {
                return ['status' => 'error', 'message' => implode(', ', $validation['errors'])];
            }
            
            // Validar que la patente existe
            $vehicleExists = $this->vehicleRepository->existsByPatente($data['patente']);
            if (!$vehicleExists) {
                return [
                    'status' => 'error', 
                    'message' => "La patente '{$data['patente']}' no está registrada en el sistema."
                ];
            }
            
            // Verificar disponibilidad (excluyendo la reserva actual)
            $disponible = $this->reservaRepository->verificarDisponibilidadParaActualizacion(
                $id,
                $data['zona'], 
                $data['fecha'], 
                $data['hora_inicio'], 
                $data['hora_fin']
            );
            
            if (!$disponible) {
                return [
                    'status' => 'error',
                    'message' => 'Ya existe una reserva en esa zona para el horario solicitado'
                ];
            }
            
            // Actualizar la reserva
            $updated = $this->reservaRepository->actualizar($id, $data);
            
            if ($updated) {
                $this->notificationService->notificarReservaActualizada($data);
                return ['status' => 'success', 'message' => 'Reserva actualizada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'Error al actualizar la reserva'];
            }
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Eliminar una reserva
     */
    public function eliminarReserva(int $id): array {
        try {
            $reserva = $this->reservaRepository->obtenerPorId($id);
            
            if (!$reserva) {
                return ['status' => 'error', 'message' => 'Reserva no encontrada'];
            }
            
            $deleted = $this->reservaRepository->eliminar($id);
            
            if ($deleted) {
                $this->notificationService->notificarReservaEliminada($reserva);
                return ['status' => 'success', 'message' => 'Reserva eliminada exitosamente'];
            } else {
                return ['status' => 'error', 'message' => 'Error al eliminar la reserva'];
            }
            
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Obtener todas las reservas
     */
    public function obtenerTodasLasReservas(): array {
        return $this->reservaRepository->obtenerTodas();
    }
    
    /**
     * Obtener reserva por ID
     */
    public function obtenerReservaPorId(int $id): ?array {
        return $this->reservaRepository->obtenerPorId($id);
    }
}
