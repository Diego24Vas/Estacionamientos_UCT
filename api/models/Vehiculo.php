<?php
require_once './config/database.php';

class Vehiculo {
    
    public static function obtenerTodos($limite = 50, $offset = 0) {
        $conn = Database::getConnection();
        $query = "SELECT v.*, z.nombre as zona_nombre, m.nombre as marca_nombre 
                  FROM INFO1170_Vehiculos v 
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON v.zona_autorizada = z.zona
                  LEFT JOIN INFO1170_MarcasVehiculos m ON v.marca = m.nombre
                  WHERE v.activo = 1 
                  ORDER BY v.fecha_registro DESC 
                  LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$limite, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorId($id) {
        $conn = Database::getConnection();
        $query = "SELECT v.*, z.nombre as zona_nombre, m.nombre as marca_nombre 
                  FROM INFO1170_Vehiculos v 
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON v.zona_autorizada = z.zona
                  LEFT JOIN INFO1170_MarcasVehiculos m ON v.marca = m.nombre
                  WHERE v.id = ? AND v.activo = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorPatente($patente) {
        $conn = Database::getConnection();
        $query = "SELECT v.*, z.nombre as zona_nombre, m.nombre as marca_nombre 
                  FROM INFO1170_Vehiculos v 
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON v.zona_autorizada = z.zona
                  LEFT JOIN INFO1170_MarcasVehiculos m ON v.marca = m.nombre
                  WHERE v.patente = ? AND v.activo = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$patente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function crear($data) {
        $conn = Database::getConnection();
        
        // Validar que la patente no exista
        if (self::patenteExiste($data['patente'])) {
            return ['error' => 'La patente ya está registrada'];
        }
        
        // Validar que la zona existe
        if (!self::zonaExiste($data['zona_autorizada'])) {
            return ['error' => 'La zona especificada no existe'];
        }
        
        $query = "INSERT INTO INFO1170_Vehiculos 
                  (propietario_nombre, propietario_apellido, propietario_email, propietario_telefono,
                   patente, tipo, marca, modelo, año, color, zona_autorizada, tipo_usuario, activo) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $data['propietario_nombre'],
            $data['propietario_apellido'],
            $data['propietario_email'],
            $data['propietario_telefono'],
            strtoupper($data['patente']),
            $data['tipo'],
            $data['marca'],
            $data['modelo'],
            $data['año'],
            $data['color'],
            $data['zona_autorizada'],
            $data['tipo_usuario']
        ]);
        
        if ($result) {
            return ['success' => true, 'id' => $conn->lastInsertId()];
        }
        
        return ['error' => 'Error al crear el vehículo'];
    }
    
    public static function actualizar($id, $data) {
        $conn = Database::getConnection();
        
        // Verificar que el vehículo existe
        $vehiculo = self::obtenerPorId($id);
        if (!$vehiculo) {
            return ['error' => 'Vehículo no encontrado'];
        }
        
        // Si se cambió la patente, validar que no exista
        if ($data['patente'] !== $vehiculo['patente'] && self::patenteExiste($data['patente'])) {
            return ['error' => 'La nueva patente ya está registrada'];
        }
        
        $query = "UPDATE INFO1170_Vehiculos SET 
                  propietario_nombre = ?, propietario_apellido = ?, propietario_email = ?,
                  propietario_telefono = ?, patente = ?, tipo = ?, marca = ?, modelo = ?,
                  año = ?, color = ?, zona_autorizada = ?, tipo_usuario = ?,
                  fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $data['propietario_nombre'],
            $data['propietario_apellido'],
            $data['propietario_email'],
            $data['propietario_telefono'],
            strtoupper($data['patente']),
            $data['tipo'],
            $data['marca'],
            $data['modelo'],
            $data['año'],
            $data['color'],
            $data['zona_autorizada'],
            $data['tipo_usuario'],
            $id
        ]);
        
        return $result ? ['success' => true] : ['error' => 'Error al actualizar el vehículo'];
    }
    
    public static function eliminar($id) {
        $conn = Database::getConnection();
        
        // Soft delete - marcar como inactivo
        $query = "UPDATE INFO1170_Vehiculos SET activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$id]);
        
        return $result ? ['success' => true] : ['error' => 'Error al eliminar el vehículo'];
    }
    
    public static function buscar($termino, $limite = 20) {
        $conn = Database::getConnection();
        $termino = "%$termino%";
        
        $query = "SELECT v.*, z.nombre as zona_nombre, m.nombre as marca_nombre 
                  FROM INFO1170_Vehiculos v 
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON v.zona_autorizada = z.zona
                  LEFT JOIN INFO1170_MarcasVehiculos m ON v.marca = m.nombre
                  WHERE v.activo = 1 AND (
                      v.patente LIKE ? OR 
                      v.propietario_nombre LIKE ? OR 
                      v.propietario_apellido LIKE ? OR
                      v.marca LIKE ? OR
                      v.modelo LIKE ?
                  ) 
                  ORDER BY v.fecha_registro DESC 
                  LIMIT ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$termino, $termino, $termino, $termino, $termino, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorZona($zona) {
        $conn = Database::getConnection();
        $query = "SELECT v.*, z.nombre as zona_nombre 
                  FROM INFO1170_Vehiculos v 
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON v.zona_autorizada = z.zona
                  WHERE v.zona_autorizada = ? AND v.activo = 1 
                  ORDER BY v.fecha_registro DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute([$zona]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function contarTotal() {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as total FROM INFO1170_Vehiculos WHERE activo = 1";
        $stmt = $conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Métodos auxiliares privados
    private static function patenteExiste($patente) {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as count FROM INFO1170_Vehiculos WHERE patente = ? AND activo = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([strtoupper($patente)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    private static function zonaExiste($zona) {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as count FROM INFO1170_ConfiguracionZonas WHERE zona = ? AND activa = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$zona]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    public static function validarDatos($data, $esActualizacion = false) {
        $errores = [];
        
        // Validaciones obligatorias
        if (empty($data['propietario_nombre'])) {
            $errores[] = 'El nombre del propietario es obligatorio';
        }
        
        if (empty($data['propietario_apellido'])) {
            $errores[] = 'El apellido del propietario es obligatorio';
        }
        
        if (empty($data['propietario_email']) || !filter_var($data['propietario_email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email del propietario es obligatorio y debe ser válido';
        }
        
        if (empty($data['patente'])) {
            $errores[] = 'La patente es obligatoria';
        } elseif (strlen($data['patente']) < 4 || strlen($data['patente']) > 8) {
            $errores[] = 'La patente debe tener entre 4 y 8 caracteres';
        }
        
        if (empty($data['tipo']) || !in_array($data['tipo'], ['auto', 'moto', 'camioneta', 'otro'])) {
            $errores[] = 'El tipo de vehículo debe ser: auto, moto, camioneta u otro';
        }
        
        if (empty($data['marca'])) {
            $errores[] = 'La marca es obligatoria';
        }
        
        if (empty($data['modelo'])) {
            $errores[] = 'El modelo es obligatorio';
        }
        
        if (empty($data['año']) || $data['año'] < 1900 || $data['año'] > date('Y') + 1) {
            $errores[] = 'El año debe ser válido';
        }
        
        if (empty($data['zona_autorizada'])) {
            $errores[] = 'La zona autorizada es obligatoria';
        }
        
        if (empty($data['tipo_usuario']) || !in_array($data['tipo_usuario'], ['estudiante', 'docente', 'administrativo', 'visitante'])) {
            $errores[] = 'El tipo de usuario debe ser: estudiante, docente, administrativo o visitante';
        }
        
        return $errores;
    }
}
?>
