<?php
require_once 'config/database.php';

class Evento {
    private $conn;
    private $tabla = "eventos";

    public $idEvento;
    public $nombre;
    public $descripcion;
    public $fecha_inicio;
    public $fecha_fin;
    public $hora_inicio;
    public $hora_fin;
    public $tipo_evento;
    public $capacidad_reservada;
    public $estado;
    public $prioridad;
    public $ubicacion;
    public $organizador;
    public $contacto;
    public $restricciones;
    public $costo_adicional;
    public $idZona;
    public $fechaCreacion;
    public $fechaActualizacion;

    // Estados válidos
    private $estadosValidos = ['planificado', 'activo', 'finalizado', 'cancelado', 'postponed'];
    
    // Tipos de evento válidos
    private $tiposValidos = ['conferencia', 'reunion', 'evento_corporativo', 'capacitacion', 'mantenimiento', 'emergencia', 'otro'];
    
    // Niveles de prioridad válidos
    private $prioridadesValidas = ['baja', 'media', 'alta', 'critica'];

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear evento
    public function crear() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (nombre, descripcion, fecha_inicio, fecha_fin, hora_inicio, hora_fin, 
                   tipo_evento, capacidad_reservada, estado, prioridad, ubicacion, 
                   organizador, contacto, restricciones, costo_adicional, idZona) 
                  VALUES (:nombre, :descripcion, :fecha_inicio, :fecha_fin, :hora_inicio, 
                          :hora_fin, :tipo_evento, :capacidad_reservada, :estado, :prioridad, 
                          :ubicacion, :organizador, :contacto, :restricciones, :costo_adicional, :idZona)";

        $stmt = $this->conn->prepare($query);

        // Validar datos requeridos
        if (empty($this->nombre) || empty($this->fecha_inicio) || empty($this->idZona)) {
            return false;
        }

        // Establecer valores por defecto
        $this->estado = $this->estado ?: 'planificado';
        $this->tipo_evento = $this->tipo_evento ?: 'otro';
        $this->prioridad = $this->prioridad ?: 'media';

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":fecha_inicio", $this->fecha_inicio);
        $stmt->bindParam(":fecha_fin", $this->fecha_fin);
        $stmt->bindParam(":hora_inicio", $this->hora_inicio);
        $stmt->bindParam(":hora_fin", $this->hora_fin);
        $stmt->bindParam(":tipo_evento", $this->tipo_evento);
        $stmt->bindParam(":capacidad_reservada", $this->capacidad_reservada);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":prioridad", $this->prioridad);
        $stmt->bindParam(":ubicacion", $this->ubicacion);
        $stmt->bindParam(":organizador", $this->organizador);
        $stmt->bindParam(":contacto", $this->contacto);
        $stmt->bindParam(":restricciones", $this->restricciones);
        $stmt->bindParam(":costo_adicional", $this->costo_adicional);
        $stmt->bindParam(":idZona", $this->idZona);

        if ($stmt->execute()) {
            $this->idEvento = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Leer todos los eventos con filtros
    public function leer($limite = 50, $offset = 0, $filtros = []) {
        $whereClause = "1=1";
        $params = [];

        if (!empty($filtros['zona'])) {
            $whereClause .= " AND e.idZona = :zona";
            $params[':zona'] = $filtros['zona'];
        }

        if (!empty($filtros['estado'])) {
            $whereClause .= " AND e.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['tipo'])) {
            $whereClause .= " AND e.tipo_evento = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['fecha_inicio'])) {
            $whereClause .= " AND e.fecha_inicio >= :fecha_inicio";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $whereClause .= " AND e.fecha_fin <= :fecha_fin";
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['prioridad'])) {
            $whereClause .= " AND e.prioridad = :prioridad";
            $params[':prioridad'] = $filtros['prioridad'];
        }

        if (!empty($filtros['organizador'])) {
            $whereClause .= " AND e.organizador LIKE :organizador";
            $params[':organizador'] = '%' . $filtros['organizador'] . '%';
        }

        $query = "SELECT e.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " e
                  LEFT JOIN zonas z ON e.idZona = z.idZona
                  WHERE " . $whereClause . "
                  ORDER BY e.fecha_inicio ASC, e.hora_inicio ASC
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
        $query = "SELECT e.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " e
                  LEFT JOIN zonas z ON e.idZona = z.idZona
                  WHERE e.idEvento = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idEvento);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->fecha_inicio = $row['fecha_inicio'];
            $this->fecha_fin = $row['fecha_fin'];
            $this->hora_inicio = $row['hora_inicio'];
            $this->hora_fin = $row['hora_fin'];
            $this->tipo_evento = $row['tipo_evento'];
            $this->capacidad_reservada = $row['capacidad_reservada'];
            $this->estado = $row['estado'];
            $this->prioridad = $row['prioridad'];
            $this->ubicacion = $row['ubicacion'];
            $this->organizador = $row['organizador'];
            $this->contacto = $row['contacto'];
            $this->restricciones = $row['restricciones'];
            $this->costo_adicional = $row['costo_adicional'];
            $this->idZona = $row['idZona'];
            $this->fechaCreacion = $row['fechaCreacion'];
            $this->fechaActualizacion = $row['fechaActualizacion'];
            return true;
        }
        return false;
    }

    // Actualizar evento
    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " 
                  SET nombre = :nombre, descripcion = :descripcion, fecha_inicio = :fecha_inicio,
                      fecha_fin = :fecha_fin, hora_inicio = :hora_inicio, hora_fin = :hora_fin,
                      tipo_evento = :tipo_evento, capacidad_reservada = :capacidad_reservada,
                      estado = :estado, prioridad = :prioridad, ubicacion = :ubicacion,
                      organizador = :organizador, contacto = :contacto, restricciones = :restricciones,
                      costo_adicional = :costo_adicional, idZona = :idZona
                  WHERE idEvento = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":fecha_inicio", $this->fecha_inicio);
        $stmt->bindParam(":fecha_fin", $this->fecha_fin);
        $stmt->bindParam(":hora_inicio", $this->hora_inicio);
        $stmt->bindParam(":hora_fin", $this->hora_fin);
        $stmt->bindParam(":tipo_evento", $this->tipo_evento);
        $stmt->bindParam(":capacidad_reservada", $this->capacidad_reservada);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":prioridad", $this->prioridad);
        $stmt->bindParam(":ubicacion", $this->ubicacion);
        $stmt->bindParam(":organizador", $this->organizador);
        $stmt->bindParam(":contacto", $this->contacto);
        $stmt->bindParam(":restricciones", $this->restricciones);
        $stmt->bindParam(":costo_adicional", $this->costo_adicional);
        $stmt->bindParam(":idZona", $this->idZona);
        $stmt->bindParam(":id", $this->idEvento);

        return $stmt->execute();
    }

    // Eliminar evento
    public function eliminar() {
        // Verificar si existen reservas asociadas al evento
        $queryReservas = "SELECT COUNT(*) as total FROM reservas WHERE idEvento = :id";
        $stmtReservas = $this->conn->prepare($queryReservas);
        $stmtReservas->bindParam(":id", $this->idEvento);
        $stmtReservas->execute();
        $reservas = $stmtReservas->fetch(PDO::FETCH_ASSOC);

        if ($reservas['total'] > 0) {
            return false; // No se puede eliminar si tiene reservas asociadas
        }

        $query = "DELETE FROM " . $this->tabla . " WHERE idEvento = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->idEvento);
        return $stmt->execute();
    }

    // Cambiar estado del evento
    public function cambiarEstado($nuevoEstado) {
        if (!in_array($nuevoEstado, $this->estadosValidos)) {
            return false;
        }

        $query = "UPDATE " . $this->tabla . " SET estado = :estado WHERE idEvento = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevoEstado);
        $stmt->bindParam(":id", $this->idEvento);

        if ($stmt->execute()) {
            $this->estado = $nuevoEstado;
            return true;
        }
        return false;
    }

    // Verificar conflictos de eventos
    public function verificarConflictos($excluirId = null) {
        $whereClause = "e.idZona = :zona 
                       AND e.estado NOT IN ('cancelado', 'finalizado')
                       AND (
                           (e.fecha_inicio <= :fecha_fin AND e.fecha_fin >= :fecha_inicio)
                       )";
        
        $params = [
            ':zona' => $this->idZona,
            ':fecha_inicio' => $this->fecha_inicio,
            ':fecha_fin' => $this->fecha_fin ?: $this->fecha_inicio
        ];

        if ($excluirId) {
            $whereClause .= " AND e.idEvento != :excluir_id";
            $params[':excluir_id'] = $excluirId;
        }

        $query = "SELECT e.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " e
                  LEFT JOIN zonas z ON e.idZona = z.idZona
                  WHERE " . $whereClause;

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener eventos activos
    public function obtenerEventosActivos($fecha = null) {
        $fecha = $fecha ?: date('Y-m-d');
        
        $query = "SELECT e.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " e
                  LEFT JOIN zonas z ON e.idZona = z.idZona
                  WHERE e.estado = 'activo' 
                    AND e.fecha_inicio <= :fecha 
                    AND (e.fecha_fin IS NULL OR e.fecha_fin >= :fecha)
                  ORDER BY e.prioridad DESC, e.fecha_inicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener próximos eventos
    public function obtenerProximosEventos($dias = 7) {
        $fechaInicio = date('Y-m-d');
        $fechaFin = date('Y-m-d', strtotime("+{$dias} days"));

        $query = "SELECT e.*, z.nombre as nombre_zona 
                  FROM " . $this->tabla . " e
                  LEFT JOIN zonas z ON e.idZona = z.idZona
                  WHERE e.estado IN ('planificado', 'activo')
                    AND e.fecha_inicio BETWEEN :fecha_inicio AND :fecha_fin
                  ORDER BY e.fecha_inicio ASC, e.hora_inicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha_inicio", $fechaInicio);
        $stmt->bindParam(":fecha_fin", $fechaFin);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas de eventos
    public function obtenerEstadisticas($fechaInicio = null, $fechaFin = null) {
        $whereClause = "1=1";
        $params = [];

        if ($fechaInicio) {
            $whereClause .= " AND fecha_inicio >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }

        if ($fechaFin) {
            $whereClause .= " AND fecha_fin <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        $query = "SELECT 
                    COUNT(*) as total_eventos,
                    SUM(CASE WHEN estado = 'planificado' THEN 1 ELSE 0 END) as planificados,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
                    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    AVG(capacidad_reservada) as promedio_capacidad,
                    SUM(CASE WHEN costo_adicional > 0 THEN costo_adicional ELSE 0 END) as total_costos
                  FROM " . $this->tabla . "
                  WHERE " . $whereClause;

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Validar datos del evento
    public function validar() {
        $errores = [];

        if (empty($this->nombre)) {
            $errores[] = "El nombre del evento es requerido";
        }

        if (empty($this->fecha_inicio)) {
            $errores[] = "La fecha de inicio es requerida";
        }

        if (empty($this->idZona)) {
            $errores[] = "La zona es requerida";
        }

        if ($this->estado && !in_array($this->estado, $this->estadosValidos)) {
            $errores[] = "Estado inválido. Estados válidos: " . implode(', ', $this->estadosValidos);
        }

        if ($this->tipo_evento && !in_array($this->tipo_evento, $this->tiposValidos)) {
            $errores[] = "Tipo de evento inválido. Tipos válidos: " . implode(', ', $this->tiposValidos);
        }

        if ($this->prioridad && !in_array($this->prioridad, $this->prioridadesValidas)) {
            $errores[] = "Prioridad inválida. Prioridades válidas: " . implode(', ', $this->prioridadesValidas);
        }

        if ($this->fecha_fin && $this->fecha_inicio && $this->fecha_fin < $this->fecha_inicio) {
            $errores[] = "La fecha de fin no puede ser anterior a la fecha de inicio";
        }

        if ($this->hora_fin && $this->hora_inicio && $this->hora_fin < $this->hora_inicio && $this->fecha_inicio == $this->fecha_fin) {
            $errores[] = "La hora de fin no puede ser anterior a la hora de inicio en el mismo día";
        }

        if ($this->capacidad_reservada !== null && $this->capacidad_reservada < 0) {
            $errores[] = "La capacidad reservada no puede ser negativa";
        }

        if ($this->costo_adicional !== null && $this->costo_adicional < 0) {
            $errores[] = "El costo adicional no puede ser negativo";
        }

        return $errores;
    }

    // Getters para arrays válidos
    public function getEstadosValidos() {
        return $this->estadosValidos;
    }

    public function getTiposValidos() {
        return $this->tiposValidos;
    }

    public function getPrioridadesValidas() {
        return $this->prioridadesValidas;
    }
}
