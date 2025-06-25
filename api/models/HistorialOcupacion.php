<?php
require_once 'config/database.php';

class HistorialOcupacion {
    private $conn;
    private $tabla = "historial_ocupacion";

    public $idHistorial;
    public $fecha;
    public $hora_inicio;
    public $hora_fin;
    public $estado;
    public $observaciones;
    public $espacios_ocupados;
    public $espacios_libres;
    public $porcentaje_ocupacion;
    public $idZona;
    public $fechaCreacion;
    public $fechaActualizacion;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear registro de historial
    public function crear() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (fecha, hora_inicio, hora_fin, estado, observaciones, espacios_ocupados, 
                   espacios_libres, porcentaje_ocupacion, idZona) 
                  VALUES (:fecha, :hora_inicio, :hora_fin, :estado, :observaciones, 
                          :espacios_ocupados, :espacios_libres, :porcentaje_ocupacion, :idZona)";

        $stmt = $this->conn->prepare($query);

        // Validar datos requeridos
        if (empty($this->fecha) || empty($this->idZona)) {
            return false;
        }

        // Calcular porcentaje si no se proporciona
        if ($this->porcentaje_ocupacion === null && $this->espacios_ocupados !== null && $this->espacios_libres !== null) {
            $total = $this->espacios_ocupados + $this->espacios_libres;
            $this->porcentaje_ocupacion = $total > 0 ? ($this->espacios_ocupados / $total) * 100 : 0;
        }

        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":hora_inicio", $this->hora_inicio);
        $stmt->bindParam(":hora_fin", $this->hora_fin);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":observaciones", $this->observaciones);
        $stmt->bindParam(":espacios_ocupados", $this->espacios_ocupados);
        $stmt->bindParam(":espacios_libres", $this->espacios_libres);
        $stmt->bindParam(":porcentaje_ocupacion", $this->porcentaje_ocupacion);
        $stmt->bindParam(":idZona", $this->idZona);

        if ($stmt->execute()) {
            $this->idHistorial = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Leer todos los registros con filtros
    public function leer($limite = 100, $offset = 0, $filtros = []) {
        $whereClause = "1=1";
        $params = [];

        if (!empty($filtros['zona'])) {
            $whereClause .= " AND idZona = :zona";
            $params[':zona'] = $filtros['zona'];
        }

        if (!empty($filtros['fecha_inicio'])) {
            $whereClause .= " AND fecha >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $whereClause .= " AND fecha <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['estado'])) {
            $whereClause .= " AND estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $query = "SELECT h.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " h
                  LEFT JOIN zonas z ON h.idZona = z.idZona
                  WHERE " . $whereClause . "
                  ORDER BY h.fecha DESC, h.hora_inicio DESC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    // Leer por ID
    public function leerPorId() {
        $query = "SELECT h.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " h
                  LEFT JOIN zonas z ON h.idZona = z.idZona
                  WHERE h.idHistorial = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idHistorial);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->fecha = $row['fecha'];
            $this->hora_inicio = $row['hora_inicio'];
            $this->hora_fin = $row['hora_fin'];
            $this->estado = $row['estado'];
            $this->observaciones = $row['observaciones'];
            $this->espacios_ocupados = $row['espacios_ocupados'];
            $this->espacios_libres = $row['espacios_libres'];
            $this->porcentaje_ocupacion = $row['porcentaje_ocupacion'];
            $this->idZona = $row['idZona'];
            $this->fechaCreacion = $row['fechaCreacion'];
            $this->fechaActualizacion = $row['fechaActualizacion'];
            return true;
        }
        return false;
    }

    // Actualizar registro
    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " 
                  SET fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin,
                      estado = :estado, observaciones = :observaciones,
                      espacios_ocupados = :espacios_ocupados, espacios_libres = :espacios_libres,
                      porcentaje_ocupacion = :porcentaje_ocupacion, idZona = :idZona
                  WHERE idHistorial = :id";

        $stmt = $this->conn->prepare($query);

        // Recalcular porcentaje si es necesario
        if ($this->porcentaje_ocupacion === null && $this->espacios_ocupados !== null && $this->espacios_libres !== null) {
            $total = $this->espacios_ocupados + $this->espacios_libres;
            $this->porcentaje_ocupacion = $total > 0 ? ($this->espacios_ocupados / $total) * 100 : 0;
        }

        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":hora_inicio", $this->hora_inicio);
        $stmt->bindParam(":hora_fin", $this->hora_fin);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":observaciones", $this->observaciones);
        $stmt->bindParam(":espacios_ocupados", $this->espacios_ocupados);
        $stmt->bindParam(":espacios_libres", $this->espacios_libres);
        $stmt->bindParam(":porcentaje_ocupacion", $this->porcentaje_ocupacion);
        $stmt->bindParam(":idZona", $this->idZona);
        $stmt->bindParam(":id", $this->idHistorial);

        return $stmt->execute();
    }

    // Eliminar registro
    public function eliminar() {
        $query = "DELETE FROM " . $this->tabla . " WHERE idHistorial = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idHistorial);
        return $stmt->execute();
    }

    // Generar snapshot de ocupación actual
    public function generarSnapshot($idZona) {
        // Obtener información actual de la zona
        $query = "SELECT 
                    z.capacidad,
                    COUNT(r.idReserva) as reservas_activas,
                    z.capacidad - COUNT(r.idReserva) as espacios_libres
                  FROM zonas z
                  LEFT JOIN reservas r ON z.idZona = r.idZona 
                    AND r.estado IN ('confirmada', 'activa')
                    AND DATE(r.fecha_reserva) = CURDATE()
                    AND CURTIME() BETWEEN r.hora_inicio AND r.hora_fin
                  WHERE z.idZona = :idZona
                  GROUP BY z.idZona, z.capacidad";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idZona", $idZona);
        $stmt->execute();

        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos) {
            $this->fecha = date('Y-m-d');
            $this->hora_inicio = date('H:i:s');
            $this->estado = 'snapshot';
            $this->espacios_ocupados = $datos['reservas_activas'];
            $this->espacios_libres = $datos['espacios_libres'];
            $this->idZona = $idZona;
            
            return $this->crear();
        }
        return false;
    }

    // Obtener estadísticas de ocupación
    public function obtenerEstadisticas($idZona = null, $fechaInicio = null, $fechaFin = null) {
        $whereClause = "1=1";
        $params = [];

        if ($idZona) {
            $whereClause .= " AND idZona = :zona";
            $params[':zona'] = $idZona;
        }

        if ($fechaInicio) {
            $whereClause .= " AND fecha >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if ($fechaFin) {
            $whereClause .= " AND fecha <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        $query = "SELECT 
                    COUNT(*) as total_registros,
                    AVG(porcentaje_ocupacion) as promedio_ocupacion,
                    MAX(porcentaje_ocupacion) as max_ocupacion,
                    MIN(porcentaje_ocupacion) as min_ocupacion,
                    AVG(espacios_ocupados) as promedio_espacios_ocupados,
                    MAX(espacios_ocupados) as max_espacios_ocupados
                  FROM " . $this->tabla . "
                  WHERE " . $whereClause . " AND porcentaje_ocupacion IS NOT NULL";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener tendencias por día de la semana
    public function obtenerTendenciasSemanal($idZona = null) {
        $whereClause = $idZona ? "WHERE idZona = :zona" : "";
        $params = $idZona ? [':zona' => $idZona] : [];

        $query = "SELECT 
                    DAYNAME(fecha) as dia_semana,
                    DAYOFWEEK(fecha) as numero_dia,
                    AVG(porcentaje_ocupacion) as promedio_ocupacion,
                    COUNT(*) as total_registros
                  FROM " . $this->tabla . "
                  " . $whereClause . "
                  GROUP BY DAYOFWEEK(fecha), DAYNAME(fecha)
                  ORDER BY numero_dia";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validar datos del historial
    public function validar() {
        $errores = [];

        if (empty($this->fecha)) {
            $errores[] = "La fecha es requerida";
        }

        if (empty($this->idZona)) {
            $errores[] = "La zona es requerida";
        }

        if ($this->espacios_ocupados !== null && $this->espacios_ocupados < 0) {
            $errores[] = "Los espacios ocupados no pueden ser negativos";
        }

        if ($this->espacios_libres !== null && $this->espacios_libres < 0) {
            $errores[] = "Los espacios libres no pueden ser negativos";
        }

        if ($this->porcentaje_ocupacion !== null && ($this->porcentaje_ocupacion < 0 || $this->porcentaje_ocupacion > 100)) {
            $errores[] = "El porcentaje de ocupación debe estar entre 0 y 100";
        }

        if ($this->hora_fin && $this->hora_inicio && $this->hora_fin < $this->hora_inicio) {
            $errores[] = "La hora de fin no puede ser anterior a la hora de inicio";
        }

        return $errores;
    }
}
