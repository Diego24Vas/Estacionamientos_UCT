<?php
require_once './config/database.php';

class Marca {
    
    public static function obtenerTodas($soloActivas = true) {
        $conn = Database::getConnection();
        $whereClause = $soloActivas ? "WHERE activa = 1" : "";
        
        $query = "SELECT * FROM INFO1170_MarcasVehiculos 
                  $whereClause 
                  ORDER BY nombre";
        
        $stmt = $conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorId($id) {
        $conn = Database::getConnection();
        $query = "SELECT * FROM INFO1170_MarcasVehiculos WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorNombre($nombre) {
        $conn = Database::getConnection();
        $query = "SELECT * FROM INFO1170_MarcasVehiculos WHERE nombre = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function crear($data) {
        $conn = Database::getConnection();
        
        // Validar que el nombre no exista
        if (self::nombreExiste($data['nombre'])) {
            return ['error' => 'La marca ya existe'];
        }
        
        $query = "INSERT INTO INFO1170_MarcasVehiculos (nombre, activa) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            ucfirst(strtolower(trim($data['nombre']))),
            $data['activa'] ?? 1
        ]);
        
        if ($result) {
            return ['success' => true, 'id' => $conn->lastInsertId()];
        }
        
        return ['error' => 'Error al crear la marca'];
    }
    
    public static function actualizar($id, $data) {
        $conn = Database::getConnection();
        
        // Verificar que la marca existe
        $marca = self::obtenerPorId($id);
        if (!$marca) {
            return ['error' => 'Marca no encontrada'];
        }
        
        // Si se cambió el nombre, validar que no exista
        if (strtolower($data['nombre']) !== strtolower($marca['nombre']) && self::nombreExiste($data['nombre'])) {
            return ['error' => 'El nuevo nombre de marca ya existe'];
        }
        
        $query = "UPDATE INFO1170_MarcasVehiculos SET nombre = ?, activa = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            ucfirst(strtolower(trim($data['nombre']))),
            $data['activa'],
            $id
        ]);
        
        return $result ? ['success' => true] : ['error' => 'Error al actualizar la marca'];
    }
    
    public static function cambiarEstado($id, $activa) {
        $conn = Database::getConnection();
        
        $query = "UPDATE INFO1170_MarcasVehiculos SET activa = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$activa, $id]);
        
        return $result ? ['success' => true] : ['error' => 'Error al cambiar el estado de la marca'];
    }
    
    public static function eliminar($id) {
        $conn = Database::getConnection();
        
        // Verificar si hay vehículos usando esta marca
        $queryCheck = "SELECT COUNT(*) as count FROM INFO1170_Vehiculos WHERE marca = (SELECT nombre FROM INFO1170_MarcasVehiculos WHERE id = ?) AND activo = 1";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute([$id]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            return ['error' => 'No se puede eliminar la marca porque tiene vehículos asociados'];
        }
        
        // Soft delete - marcar como inactiva
        $query = "UPDATE INFO1170_MarcasVehiculos SET activa = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $resultado = $stmt->execute([$id]);
        
        return $resultado ? ['success' => true] : ['error' => 'Error al eliminar la marca'];
    }
    
    public static function buscar($termino, $limite = 20) {
        $conn = Database::getConnection();
        $termino = "%$termino%";
        
        $query = "SELECT * FROM INFO1170_MarcasVehiculos 
                  WHERE nombre LIKE ? AND activa = 1 
                  ORDER BY nombre 
                  LIMIT ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$termino, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerConEstadisticas() {
        $conn = Database::getConnection();
        
        $query = "SELECT m.*, 
                  COUNT(v.id) as total_vehiculos,
                  COUNT(CASE WHEN v.activo = 1 THEN 1 END) as vehiculos_activos
                  FROM INFO1170_MarcasVehiculos m
                  LEFT JOIN INFO1170_Vehiculos v ON m.nombre = v.marca
                  WHERE m.activa = 1
                  GROUP BY m.id, m.nombre, m.activa, m.fecha_creacion
                  ORDER BY vehiculos_activos DESC, m.nombre";
        
        $stmt = $conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerMasUsadas($limite = 10) {
        $conn = Database::getConnection();
        
        $query = "SELECT m.*, COUNT(v.id) as total_vehiculos
                  FROM INFO1170_MarcasVehiculos m
                  INNER JOIN INFO1170_Vehiculos v ON m.nombre = v.marca
                  WHERE m.activa = 1 AND v.activo = 1
                  GROUP BY m.id
                  ORDER BY total_vehiculos DESC
                  LIMIT ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private static function nombreExiste($nombre) {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as count FROM INFO1170_MarcasVehiculos WHERE LOWER(nombre) = LOWER(?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([trim($nombre)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    public static function validarDatos($data, $esActualizacion = false) {
        $errores = [];
        
        if (empty($data['nombre'])) {
            $errores[] = 'El nombre de la marca es obligatorio';
        } elseif (strlen(trim($data['nombre'])) < 2) {
            $errores[] = 'El nombre de la marca debe tener al menos 2 caracteres';
        } elseif (strlen(trim($data['nombre'])) > 50) {
            $errores[] = 'El nombre de la marca no puede exceder 50 caracteres';
        }
        
        if (isset($data['activa']) && !in_array($data['activa'], [0, 1, true, false])) {
            $errores[] = 'El estado activa debe ser verdadero o falso';
        }
        
        return $errores;
    }
    
    public static function importarMarcasComunes() {
        $marcasComunes = [
            'Toyota', 'Chevrolet', 'Nissan', 'Hyundai', 'Kia', 'Suzuki',
            'Mitsubishi', 'Mazda', 'Subaru', 'Honda', 'Ford', 'Volkswagen',
            'Peugeot', 'Renault', 'Fiat', 'Citroen', 'BMW', 'Mercedes-Benz',
            'Audi', 'Volvo', 'Jeep', 'Land Rover', 'Porsche', 'Ferrari',
            'Lamborghini', 'Maserati', 'Jaguar', 'Alfa Romeo', 'Mini',
            'Smart', 'Tesla', 'BYD', 'Geely', 'Chery', 'Great Wall'
        ];
        
        $conn = Database::getConnection();
        $creadas = 0;
        
        foreach ($marcasComunes as $marca) {
            if (!self::nombreExiste($marca)) {
                $result = self::crear(['nombre' => $marca, 'activa' => 1]);
                if (isset($result['success'])) {
                    $creadas++;
                }
            }
        }
        
        return ['success' => true, 'marcas_creadas' => $creadas];
    }
}
?>
