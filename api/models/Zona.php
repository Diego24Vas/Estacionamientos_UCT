<?php
require_once './config/database.php';

class Zona {
    
    public static function obtenerTodas($soloActivas = true) {
        $conn = Database::getConnection();
        $whereClause = $soloActivas ? "WHERE activa = 1" : "";
        
        $query = "SELECT *, 
                  (cupo_maximo - cupo_reservado) as cupo_disponible,
                  ROUND((cupo_reservado / cupo_maximo) * 100, 2) as porcentaje_ocupacion
                  FROM INFO1170_ConfiguracionZonas 
                  $whereClause 
                  ORDER BY zona";
        
        $stmt = $conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorCodigo($zona) {
        $conn = Database::getConnection();
        $query = "SELECT *, 
                  (cupo_maximo - cupo_reservado) as cupo_disponible,
                  ROUND((cupo_reservado / cupo_maximo) * 100, 2) as porcentaje_ocupacion
                  FROM INFO1170_ConfiguracionZonas 
                  WHERE zona = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$zona]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorId($id) {
        $conn = Database::getConnection();
        $query = "SELECT *, 
                  (cupo_maximo - cupo_reservado) as cupo_disponible,
                  ROUND((cupo_reservado / cupo_maximo) * 100, 2) as porcentaje_ocupacion
                  FROM INFO1170_ConfiguracionZonas 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function crear($data) {
        $conn = Database::getConnection();
        
        // Validar que el código de zona no exista
        if (self::codigoZonaExiste($data['zona'])) {
            return ['error' => 'El código de zona ya existe'];
        }
        
        $query = "INSERT INTO INFO1170_ConfiguracionZonas 
                  (zona, nombre, descripcion, cupo_maximo, cupo_reservado, activa, color_zona) 
                  VALUES (?, ?, ?, ?, 0, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            strtoupper($data['zona']),
            $data['nombre'],
            $data['descripcion'],
            $data['cupo_maximo'],
            $data['activa'] ?? 1,
            $data['color_zona'] ?? '#007bff'
        ]);
        
        if ($result) {
            return ['success' => true, 'id' => $conn->lastInsertId()];
        }
        
        return ['error' => 'Error al crear la zona'];
    }
    
    public static function actualizar($id, $data) {
        $conn = Database::getConnection();
        
        // Verificar que la zona existe
        $zona = self::obtenerPorId($id);
        if (!$zona) {
            return ['error' => 'Zona no encontrada'];
        }
        
        // Si se cambió el código, validar que no exista
        if ($data['zona'] !== $zona['zona'] && self::codigoZonaExiste($data['zona'])) {
            return ['error' => 'El nuevo código de zona ya existe'];
        }
        
        $query = "UPDATE INFO1170_ConfiguracionZonas SET 
                  zona = ?, nombre = ?, descripcion = ?, cupo_maximo = ?, 
                  activa = ?, color_zona = ?, fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            strtoupper($data['zona']),
            $data['nombre'],
            $data['descripcion'],
            $data['cupo_maximo'],
            $data['activa'],
            $data['color_zona'],
            $id
        ]);
        
        return $result ? ['success' => true] : ['error' => 'Error al actualizar la zona'];
    }
    
    public static function cambiarEstado($id, $activa) {
        $conn = Database::getConnection();
        
        $query = "UPDATE INFO1170_ConfiguracionZonas SET 
                  activa = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$activa, $id]);
        
        return $result ? ['success' => true] : ['error' => 'Error al cambiar el estado de la zona'];
    }
    
    public static function actualizarCupoReservado($zona, $incremento) {
        $conn = Database::getConnection();
        
        // Verificar disponibilidad antes de incrementar
        if ($incremento > 0) {
            $zonaInfo = self::obtenerPorCodigo($zona);
            if (!$zonaInfo || ($zonaInfo['cupo_reservado'] + $incremento) > $zonaInfo['cupo_maximo']) {
                return ['error' => 'No hay cupo disponible en la zona'];
            }
        }
        
        $query = "UPDATE INFO1170_ConfiguracionZonas SET 
                  cupo_reservado = GREATEST(0, cupo_reservado + ?),
                  fecha_actualizacion = CURRENT_TIMESTAMP 
                  WHERE zona = ? AND activa = 1";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$incremento, $zona]);
        
        if ($result && $stmt->rowCount() > 0) {
            // Registrar en historial
            self::registrarEnHistorial($zona);
            return ['success' => true];
        }
        
        return ['error' => 'Error al actualizar cupo'];
    }
    
    public static function obtenerDisponibilidad($zona = null) {
        $conn = Database::getConnection();
        
        if ($zona) {
            $query = "SELECT zona, nombre, cupo_maximo, cupo_reservado,
                      (cupo_maximo - cupo_reservado) as cupo_disponible,
                      ROUND((cupo_reservado / cupo_maximo) * 100, 2) as porcentaje_ocupacion,
                      CASE 
                          WHEN cupo_reservado >= cupo_maximo THEN 'completo'
                          WHEN (cupo_reservado / cupo_maximo) >= 0.8 THEN 'casi_completo'
                          ELSE 'disponible'
                      END as estado
                      FROM INFO1170_ConfiguracionZonas 
                      WHERE zona = ? AND activa = 1";
            $stmt = $conn->prepare($query);
            $stmt->execute([$zona]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $query = "SELECT zona, nombre, cupo_maximo, cupo_reservado,
                      (cupo_maximo - cupo_reservado) as cupo_disponible,
                      ROUND((cupo_reservado / cupo_maximo) * 100, 2) as porcentaje_ocupacion,
                      CASE 
                          WHEN cupo_reservado >= cupo_maximo THEN 'completo'
                          WHEN (cupo_reservado / cupo_maximo) >= 0.8 THEN 'casi_completo'
                          ELSE 'disponible'
                      END as estado
                      FROM INFO1170_ConfiguracionZonas 
                      WHERE activa = 1 
                      ORDER BY zona";
            $stmt = $conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    public static function obtenerEstadisticas() {
        $conn = Database::getConnection();
        
        $query = "SELECT 
                  COUNT(*) as total_zonas,
                  SUM(cupo_maximo) as cupo_total,
                  SUM(cupo_reservado) as cupo_ocupado,
                  SUM(cupo_maximo - cupo_reservado) as cupo_disponible,
                  ROUND(AVG((cupo_reservado / cupo_maximo) * 100), 2) as porcentaje_promedio,
                  COUNT(CASE WHEN cupo_reservado >= cupo_maximo THEN 1 END) as zonas_completas
                  FROM INFO1170_ConfiguracionZonas 
                  WHERE activa = 1";
        
        $stmt = $conn->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private static function codigoZonaExiste($zona) {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as count FROM INFO1170_ConfiguracionZonas WHERE zona = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([strtoupper($zona)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    private static function registrarEnHistorial($zona) {
        $conn = Database::getConnection();
        
        $zonaInfo = self::obtenerPorCodigo($zona);
        if (!$zonaInfo) return false;
        
        $query = "INSERT INTO INFO1170_HistorialOcupacion 
                  (zona, fecha, hora, espacios_ocupados, espacios_totales, 
                   porcentaje_ocupacion, tipo_registro) 
                  VALUES (?, CURDATE(), CURTIME(), ?, ?, ?, 'automatico')";
        
        $stmt = $conn->prepare($query);
        return $stmt->execute([
            $zona,
            $zonaInfo['cupo_reservado'],
            $zonaInfo['cupo_maximo'],
            round(($zonaInfo['cupo_reservado'] / $zonaInfo['cupo_maximo']) * 100, 2)
        ]);
    }
    
    public static function validarDatos($data, $esActualizacion = false) {
        $errores = [];
        
        if (empty($data['zona'])) {
            $errores[] = 'El código de zona es obligatorio';
        } elseif (strlen($data['zona']) > 10) {
            $errores[] = 'El código de zona no puede exceder 10 caracteres';
        }
        
        if (empty($data['nombre'])) {
            $errores[] = 'El nombre de la zona es obligatorio';
        } elseif (strlen($data['nombre']) > 100) {
            $errores[] = 'El nombre no puede exceder 100 caracteres';
        }
        
        if (empty($data['cupo_maximo']) || !is_numeric($data['cupo_maximo']) || $data['cupo_maximo'] <= 0) {
            $errores[] = 'El cupo máximo debe ser un número mayor a 0';
        }
        
        if (isset($data['color_zona']) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $data['color_zona'])) {
            $errores[] = 'El color debe ser un código hexadecimal válido (#RRGGBB)';
        }
        
        return $errores;
    }
}
?>
