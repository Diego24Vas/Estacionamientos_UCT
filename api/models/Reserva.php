<?php
require_once './config/database.php';

class Reserva {
    
    public static function obtenerTodas($limite = 50, $offset = 0) {
        $conn = Database::getConnection();
        $query = "SELECT r.*, z.nombre as zona_nombre, v.patente, v.propietario_nombre, v.propietario_apellido
                  FROM INFO1170_Reservas r
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
                  LEFT JOIN INFO1170_Vehiculos v ON r.patente = v.patente
                  ORDER BY r.fecha_creacion DESC
                  LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$limite, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorId($id) {
        $conn = Database::getConnection();
        $query = "SELECT r.*, z.nombre as zona_nombre, v.patente, v.propietario_nombre, v.propietario_apellido
                  FROM INFO1170_Reservas r
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
                  LEFT JOIN INFO1170_Vehiculos v ON r.patente = v.patente
                  WHERE r.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function crear($data) {
        $conn = Database::getConnection();
        
        // Validar disponibilidad de zona
        if (!self::validarDisponibilidadZona($data['zona'], $data['fecha'], $data['hora_inicio'], $data['hora_fin'])) {
            return ['error' => 'No hay cupo disponible en la zona para el horario solicitado'];
        }
        
        // Validar que el vehículo no tenga otra reserva activa
        if (self::vehiculoTieneReservaActiva($data['patente'], $data['fecha'])) {
            return ['error' => 'El vehículo ya tiene una reserva activa para esta fecha'];
        }
        
        $query = "INSERT INTO INFO1170_Reservas 
                  (evento, fecha, zona, hora_inicio, hora_fin, usuario, patente, numero_espacio, estado, observaciones) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'reservado', ?)";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $data['evento'],
            $data['fecha'],
            $data['zona'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['usuario'],
            $data['patente'],
            $data['numero_espacio'] ?? null,
            $data['observaciones'] ?? null
        ]);
        
        if ($result) {
            $reservaId = $conn->lastInsertId();
            
            // Actualizar cupo de zona
            require_once './models/Zona.php';
            Zona::actualizarCupoReservado($data['zona'], 1);
            
            return ['success' => true, 'id' => $reservaId];
        }
        
        return ['error' => 'Error al crear la reserva'];
    }
    
    public static function actualizar($id, $data) {
        $conn = Database::getConnection();
        
        $reserva = self::obtenerPorId($id);
        if (!$reserva) {
            return ['error' => 'Reserva no encontrada'];
        }
        
        // Si se cambia la zona o fecha, validar disponibilidad
        if (($data['zona'] !== $reserva['zona']) || ($data['fecha'] !== $reserva['fecha'])) {
            if (!self::validarDisponibilidadZona($data['zona'], $data['fecha'], $data['hora_inicio'], $data['hora_fin'], $id)) {
                return ['error' => 'No hay cupo disponible en la nueva zona/fecha'];
            }
        }
        
        $query = "UPDATE INFO1170_Reservas SET 
                  evento = ?, fecha = ?, zona = ?, hora_inicio = ?, hora_fin = ?,
                  usuario = ?, patente = ?, numero_espacio = ?, observaciones = ?
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $data['evento'],
            $data['fecha'],
            $data['zona'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['usuario'],
            $data['patente'],
            $data['numero_espacio'],
            $data['observaciones'],
            $id
        ]);
        
        if ($result) {
            // Si cambió de zona, actualizar cupos
            if ($data['zona'] !== $reserva['zona']) {
                require_once './models/Zona.php';
                Zona::actualizarCupoReservado($reserva['zona'], -1); // Liberar zona anterior
                Zona::actualizarCupoReservado($data['zona'], 1);     // Ocupar nueva zona
            }
            
            return ['success' => true];
        }
        
        return ['error' => 'Error al actualizar la reserva'];
    }
    
    public static function cambiarEstado($id, $nuevoEstado) {
        $conn = Database::getConnection();
        
        $reserva = self::obtenerPorId($id);
        if (!$reserva) {
            return ['error' => 'Reserva no encontrada'];
        }
        
        $query = "UPDATE INFO1170_Reservas SET estado = ?";
        $params = [$nuevoEstado];
        
        // Si se libera o cancela, agregar fecha de liberación
        if (in_array($nuevoEstado, ['liberado', 'cancelado'])) {
            $query .= ", fecha_liberacion = CURRENT_TIMESTAMP";
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $conn->prepare($query);
        $result = $stmt->execute($params);
        
        if ($result) {
            // Si se libera o cancela, actualizar cupo de zona
            if (in_array($nuevoEstado, ['liberado', 'cancelado']) && $reserva['estado'] === 'reservado') {
                require_once './models/Zona.php';
                Zona::actualizarCupoReservado($reserva['zona'], -1);
            }
            
            return ['success' => true];
        }
        
        return ['error' => 'Error al cambiar el estado de la reserva'];
    }
    
    public static function obtenerPorUsuario($usuario, $limite = 50) {
        $conn = Database::getConnection();
        $query = "SELECT r.*, z.nombre as zona_nombre, v.patente, v.propietario_nombre
                  FROM INFO1170_Reservas r
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
                  LEFT JOIN INFO1170_Vehiculos v ON r.patente = v.patente
                  WHERE r.usuario = ?
                  ORDER BY r.fecha DESC, r.hora_inicio DESC
                  LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorZona($zona, $fecha = null) {
        $conn = Database::getConnection();
        
        $query = "SELECT r.*, v.propietario_nombre, v.propietario_apellido
                  FROM INFO1170_Reservas r
                  LEFT JOIN INFO1170_Vehiculos v ON r.patente = v.patente
                  WHERE r.zona = ?";
        $params = [$zona];
        
        if ($fecha) {
            $query .= " AND r.fecha = ?";
            $params[] = $fecha;
        }
        
        $query .= " ORDER BY r.fecha DESC, r.hora_inicio ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerPorFecha($fecha, $zona = null) {
        $conn = Database::getConnection();
        
        $query = "SELECT r.*, z.nombre as zona_nombre, v.propietario_nombre, v.propietario_apellido
                  FROM INFO1170_Reservas r
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
                  LEFT JOIN INFO1170_Vehiculos v ON r.patente = v.patente
                  WHERE r.fecha = ?";
        $params = [$fecha];
        
        if ($zona) {
            $query .= " AND r.zona = ?";
            $params[] = $zona;
        }
        
        $query .= " ORDER BY r.zona, r.hora_inicio ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function buscar($termino, $limite = 20) {
        $conn = Database::getConnection();
        $termino = "%$termino%";
        
        $query = "SELECT r.*, z.nombre as zona_nombre, v.propietario_nombre, v.propietario_apellido
                  FROM INFO1170_Reservas r
                  LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
                  LEFT JOIN INFO1170_Vehiculos v ON r.patente = v.patente
                  WHERE (r.evento LIKE ? OR r.usuario LIKE ? OR r.patente LIKE ? OR 
                         v.propietario_nombre LIKE ? OR v.propietario_apellido LIKE ?)
                  ORDER BY r.fecha_creacion DESC
                  LIMIT ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$termino, $termino, $termino, $termino, $termino, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function contarTotal() {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as total FROM INFO1170_Reservas";
        $stmt = $conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public static function obtenerEstadisticas($fechaInicio = null, $fechaFin = null) {
        $conn = Database::getConnection();
        
        $whereClause = "";
        $params = [];
        
        if ($fechaInicio && $fechaFin) {
            $whereClause = "WHERE fecha BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        } elseif ($fechaInicio) {
            $whereClause = "WHERE fecha >= ?";
            $params = [$fechaInicio];
        }
        
        $query = "SELECT 
                  COUNT(*) as total_reservas,
                  COUNT(CASE WHEN estado = 'reservado' THEN 1 END) as reservas_activas,
                  COUNT(CASE WHEN estado = 'liberado' THEN 1 END) as reservas_liberadas,
                  COUNT(CASE WHEN estado = 'cancelado' THEN 1 END) as reservas_canceladas,
                  COUNT(DISTINCT zona) as zonas_utilizadas,
                  COUNT(DISTINCT patente) as vehiculos_diferentes
                  FROM INFO1170_Reservas $whereClause";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private static function validarDisponibilidadZona($zona, $fecha, $horaInicio, $horaFin, $excluirReservaId = null) {
        $conn = Database::getConnection();
        
        // Verificar que la zona existe y está activa
        require_once './models/Zona.php';
        $zonaInfo = Zona::obtenerPorCodigo($zona);
        if (!$zonaInfo || !$zonaInfo['activa']) {
            return false;
        }
        
        // Verificar cupo disponible
        if ($zonaInfo['cupo_disponible'] <= 0) {
            return false;
        }
        
        // Verificar conflictos de horario (opcional, dependiendo de la lógica de negocio)
        $query = "SELECT COUNT(*) as count FROM INFO1170_Reservas 
                  WHERE zona = ? AND fecha = ? AND estado = 'reservado'
                  AND ((hora_inicio <= ? AND hora_fin > ?) OR (hora_inicio < ? AND hora_fin >= ?))";
        $params = [$zona, $fecha, $horaInicio, $horaInicio, $horaFin, $horaFin];
        
        if ($excluirReservaId) {
            $query .= " AND id != ?";
            $params[] = $excluirReservaId;
        }
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }
    
    private static function vehiculoTieneReservaActiva($patente, $fecha) {
        $conn = Database::getConnection();
        $query = "SELECT COUNT(*) as count FROM INFO1170_Reservas 
                  WHERE patente = ? AND fecha = ? AND estado = 'reservado'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$patente, $fecha]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    public static function validarDatos($data, $esActualizacion = false) {
        $errores = [];
        
        if (empty($data['evento'])) {
            $errores[] = 'El evento es obligatorio';
        }
        
        if (empty($data['fecha'])) {
            $errores[] = 'La fecha es obligatoria';
        } elseif (!self::validarFecha($data['fecha'])) {
            $errores[] = 'La fecha debe ser válida y no puede ser anterior a hoy';
        }
        
        if (empty($data['zona'])) {
            $errores[] = 'La zona es obligatoria';
        }
        
        if (empty($data['hora_inicio'])) {
            $errores[] = 'La hora de inicio es obligatoria';
        }
        
        if (empty($data['hora_fin'])) {
            $errores[] = 'La hora de fin es obligatoria';
        }
        
        if (!empty($data['hora_inicio']) && !empty($data['hora_fin'])) {
            if (strtotime($data['hora_fin']) <= strtotime($data['hora_inicio'])) {
                $errores[] = 'La hora de fin debe ser posterior a la hora de inicio';
            }
        }
        
        if (empty($data['usuario'])) {
            $errores[] = 'El usuario es obligatorio';
        }
        
        if (empty($data['patente'])) {
            $errores[] = 'La patente es obligatoria';
        }
        
        return $errores;
    }
    
    private static function validarFecha($fecha) {
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fechaObj) {
            return false;
        }
        
        $hoy = new DateTime();
        return $fechaObj >= $hoy->setTime(0, 0, 0);
    }
}
