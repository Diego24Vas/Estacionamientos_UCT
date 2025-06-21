<?php

/**
 * Servicio de validaciones usando DI
 */
class ValidationService {
    
    /**
     * Validar datos de reserva
     */
    public function validateReserva(array $data): array {
        $errors = [];
        
        // Validar campos requeridos
        $required = ['evento', 'fecha', 'hora_inicio', 'hora_fin', 'usuario', 'patente', 'zona'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "El campo '{$field}' es requerido";
            }
        }
        
        // Validar fecha
        if (!empty($data['fecha'])) {
            if (strtotime($data['fecha']) < strtotime(date('Y-m-d'))) {
                $errors[] = "No se puede reservar para fechas pasadas";
            }
        }
        
        // Validar horarios
        if (!empty($data['hora_inicio']) && !empty($data['hora_fin'])) {
            if (strtotime($data['hora_fin']) <= strtotime($data['hora_inicio'])) {
                $errors[] = "La hora de fin debe ser posterior a la hora de inicio";
            }
        }
        
        // Validar patente
        if (!empty($data['patente'])) {
            $patente = strtoupper(trim($data['patente']));
            if (strlen($patente) < 4 || strlen($patente) > 8) {
                $errors[] = "La patente debe tener entre 4 y 8 caracteres";
            }
            $data['patente'] = $patente; // Normalizar
        }
        
        // Validar zona
        if (!empty($data['zona'])) {
            $zonasValidas = ['A', 'B', 'C', 'D'];
            if (!in_array($data['zona'], $zonasValidas)) {
                $errors[] = "Zona inválida. Debe ser A, B, C o D";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }
    
    /**
     * Validar datos de vehículo
     */
    public function validateVehicle(array $data): array {
        $errors = [];
        
        $required = ['patente', 'marca', 'modelo', 'tipo'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "El campo '{$field}' es requerido";
            }
        }
        
        // Validar patente
        if (!empty($data['patente'])) {
            $patente = strtoupper(trim($data['patente']));
            if (strlen($patente) < 4 || strlen($patente) > 8) {
                $errors[] = "La patente debe tener entre 4 y 8 caracteres";
            }
            $data['patente'] = $patente;
        }
        
        // Validar tipo de vehículo
        if (!empty($data['tipo'])) {
            $tiposValidos = ['auto', 'moto', 'camioneta', 'otros'];
            if (!in_array(strtolower($data['tipo']), $tiposValidos)) {
                $errors[] = "Tipo de vehículo inválido";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }
    
    /**
     * Validar formato de email
     */
    public function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validar formato de teléfono
     */
    public function validatePhone(string $phone): bool {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 8 && strlen($phone) <= 15;
    }
    
    /**
     * Sanitizar string
     */
    public function sanitizeString(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
