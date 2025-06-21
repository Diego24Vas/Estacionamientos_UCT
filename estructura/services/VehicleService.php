<?php

require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';

/**
 * Vehicle Service
 * Servicio para manejo de vehículos usando DI
 */
class VehicleService {
    private $vehicleRepository;
    private $validationService;
    
    public function __construct($vehicleRepository, ValidationService $validationService) {
        $this->vehicleRepository = $vehicleRepository;
        $this->validationService = $validationService;
    }
    
    /**
     * Validar una patente
     */
    public function validatePatente(string $patente): array {
        try {
            // Validación de formato
            if (!$this->validationService->isValidPatente($patente)) {
                return [
                    'valida' => false,
                    'mensaje' => 'Formato de patente inválido',
                    'tipo' => 'error'
                ];
            }
            
            // Verificar si existe en BD
            if ($this->patenteExists($patente)) {
                return [
                    'valida' => true,
                    'mensaje' => 'Patente válida y registrada',
                    'tipo' => 'success'
                ];
            } else {
                return [
                    'valida' => false,
                    'mensaje' => 'Patente no encontrada en el sistema. Debe registrar el vehículo primero.',
                    'tipo' => 'warning'
                ];
            }
        } catch (Exception $e) {
            return [
                'valida' => false,
                'mensaje' => 'Error al validar patente: ' . $e->getMessage(),
                'tipo' => 'error'
            ];
        }
    }
    
    /**
     * Verificar si una patente existe en la base de datos
     */
    private function patenteExists(string $patente): bool {
        try {
            // Si tenemos un repository específico para vehículos, usarlo
            if (method_exists($this->vehicleRepository, 'findByPatente')) {
                $vehicle = $this->vehicleRepository->findByPatente($patente);
                return $vehicle !== null;
            }
            
            // Fallback a consulta directa
            global $conexion;
            if (!$conexion) {
                throw new Exception("No hay conexión a la base de datos");
            }
            
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM vehiculos WHERE patente = ?");
            $stmt->bind_param("s", $patente);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_row()[0];
            $stmt->close();
            
            return $count > 0;
        } catch (Exception $e) {
            error_log("Error verificando patente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener información completa de un vehículo por patente
     */
    public function getVehicleByPatente(string $patente): ?array {
        try {
            if (method_exists($this->vehicleRepository, 'findByPatente')) {
                return $this->vehicleRepository->findByPatente($patente);
            }
            
            // Fallback a consulta directa
            global $conexion;
            if (!$conexion) {
                return null;
            }
            
            $stmt = $conexion->prepare("
                SELECT v.*, m.nombre as marca_nombre 
                FROM vehiculos v 
                LEFT JOIN marcas m ON v.marca_id = m.id 
                WHERE v.patente = ?
            ");
            $stmt->bind_param("s", $patente);
            $stmt->execute();
            $result = $stmt->get_result();
            $vehicle = $result->fetch_assoc();
            $stmt->close();
            
            return $vehicle ?: null;
        } catch (Exception $e) {
            error_log("Error obteniendo vehículo: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Registrar nuevo vehículo
     */
    public function createVehicle(array $data): array {
        try {
            // Validar datos
            $validation = $this->validateVehicleData($data);
            if (!$validation['valido']) {
                return $validation;
            }
            
            // Verificar que la patente no exista
            if ($this->patenteExists($data['patente'])) {
                return [
                    'exito' => false,
                    'mensaje' => 'La patente ya está registrada en el sistema'
                ];
            }
            
            if (method_exists($this->vehicleRepository, 'create')) {
                $id = $this->vehicleRepository->create($data);
                return [
                    'exito' => true,
                    'mensaje' => 'Vehículo registrado exitosamente',
                    'id' => $id
                ];
            }
            
            // Fallback a inserción directa
            global $conexion;
            $stmt = $conexion->prepare("
                INSERT INTO vehiculos (patente, marca_id, modelo, color, año, propietario) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sissss", 
                $data['patente'],
                $data['marca_id'],
                $data['modelo'],
                $data['color'],
                $data['año'],
                $data['propietario']
            );
            
            if ($stmt->execute()) {
                $id = $conexion->insert_id;
                $stmt->close();
                return [
                    'exito' => true,
                    'mensaje' => 'Vehículo registrado exitosamente',
                    'id' => $id
                ];
            } else {
                $stmt->close();
                return [
                    'exito' => false,
                    'mensaje' => 'Error al registrar el vehículo'
                ];
            }
        } catch (Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Validar datos de vehículo
     */
    private function validateVehicleData(array $data): array {
        $errors = [];
        
        if (empty($data['patente'])) {
            $errors[] = 'La patente es requerida';
        } elseif (!$this->validationService->isValidPatente($data['patente'])) {
            $errors[] = 'Formato de patente inválido';
        }
        
        if (empty($data['marca_id'])) {
            $errors[] = 'La marca es requerida';
        }
        
        if (empty($data['modelo'])) {
            $errors[] = 'El modelo es requerido';
        }
        
        if (empty($data['propietario'])) {
            $errors[] = 'El propietario es requerido';
        }
        
        return [
            'valido' => empty($errors),
            'errores' => $errors,
            'mensaje' => empty($errors) ? 'Datos válidos' : 'Datos inválidos: ' . implode(', ', $errors)
        ];
    }
}
