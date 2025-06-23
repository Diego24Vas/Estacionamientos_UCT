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
                return [
                    'exito' => false,
                    'mensaje' => $validation['mensaje']
                ];
            }
            
            // Verificar que la patente no exista
            if ($this->patenteExists($data['patente'])) {
                return [
                    'exito' => false,
                    'mensaje' => 'La patente ' . $data['patente'] . ' ya está registrada en el sistema'
                ];
            }
            
            // Usar repository si está disponible
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
            if (!$conexion) {
                throw new Exception("No hay conexión a la base de datos");
            }
            
            $stmt = $conexion->prepare("
                INSERT INTO vehiculos (
                    propietario_nombre, propietario_apellido, propietario_email, propietario_telefono,
                    patente, tipo, marca, modelo, año, color, zona_autorizada, tipo_usuario, fecha_registro
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param("ssssssssssss", 
                $data['propietario_nombre'],
                $data['propietario_apellido'], 
                $data['propietario_email'],
                $data['propietario_telefono'],
                $data['patente'],
                $data['tipo'],
                $data['marca'],
                $data['modelo'],
                $data['año'],
                $data['color'],
                $data['zona_autorizada'],
                $data['tipo_usuario']
            );
            
            if ($stmt->execute()) {
                $id = $conexion->insert_id;
                $stmt->close();
                return [
                    'exito' => true,
                    'mensaje' => 'Vehículo registrado exitosamente con ID: ' . $id,
                    'id' => $id
                ];
            } else {
                $error = $stmt->error;
                $stmt->close();
                return [
                    'exito' => false,
                    'mensaje' => 'Error al registrar el vehículo: ' . $error
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error creating vehicle: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error al registrar vehículo: ' . $e->getMessage()
            ];
        }
    }
      /**
     * Validar datos de vehículo
     */    private function validateVehicleData(array $data): array {
        $errors = [];
        
        // Validaciones requeridas
        if (empty($data['propietario_nombre'])) {
            $errors[] = 'El nombre del propietario es requerido';
        }
        
        if (empty($data['propietario_apellido'])) {
            $errors[] = 'El apellido del propietario es requerido';
        }
        
        if (empty($data['patente'])) {
            $errors[] = 'La patente es requerida';
        } elseif (!$this->validationService->isValidPatente($data['patente'])) {
            $errors[] = 'Formato de patente inválido';
        }
        
        if (empty($data['zona_autorizada'])) {
            $errors[] = 'La zona autorizada es requerida';
        }
        
        // Validaciones opcionales
        if (!empty($data['propietario_email']) && !filter_var($data['propietario_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        if (!empty($data['año'])) {
            $currentYear = (int)date('Y');
            $year = (int)$data['año'];
            if ($year < 1990 || $year > ($currentYear + 1)) {
                $errors[] = 'Año del vehículo inválido';
            }        }
        
        return [
            'valido' => empty($errors),
            'errores' => $errors,
            'mensaje' => empty($errors) ? 'Datos válidos' : 'Errores encontrados: ' . implode(', ', $errors)
        ];
    }
}
