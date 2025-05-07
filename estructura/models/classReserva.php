<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once CONFIG_PATH . '/conex.php';

// Interfaz para el patrón Adapter
interface IReserva {
    public function crearReserva();
    public function actualizarReserva();
    public function eliminarReserva();
    public function obtenerReservaPorId();
    public function obtenerTodasLasReservas();
    public function verificarDisponibilidad();
}

// Clase abstracta para el patrón Factory Method
abstract class ReservaFactory {
    abstract public function crearReserva($datos);
}

// Implementación concreta del Factory
class ReservaFactoryImpl extends ReservaFactory {
    public function crearReserva($datos) {
        return new Reserva($datos);
    }
}

// Interfaz para los observadores
interface ReservaObserver {
    public function update($message);
}

// Clase para manejar las notificaciones
class ReservaSubject {
    private $observers = [];

    public function attach(ReservaObserver $observer) {
        $this->observers[] = $observer;
    }

    public function detach(ReservaObserver $observer) {
        $key = array_search($observer, $this->observers);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify($message) {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }
}

// Clase para manejar las validaciones
class ReservaValidator {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function validarDatos($reserva) {
        if (empty($reserva->getEvento()) || empty($reserva->getFecha()) || 
            empty($reserva->getHoraInicio()) || empty($reserva->getHoraFin()) || 
            empty($reserva->getZona()) || empty($reserva->getTipoVehiculo())) {
            throw new Exception("Todos los campos son obligatorios");
        }

        if (!$this->validarFechaHora($reserva)) {
            throw new Exception("La fecha y hora no son válidas");
        }

        if (!$this->validarFormatoFechaHora($reserva)) {
            throw new Exception("El formato de fecha y hora no es válido");
        }

        if (!$this->validarTipoVehiculo($reserva)) {
            throw new Exception("El tipo de vehículo no está permitido en esta zona");
        }

        if (!$this->validarCapacidad($reserva)) {
            throw new Exception("La zona ha alcanzado su capacidad máxima");
        }

        if (!$this->validarPermisos($reserva)) {
            throw new Exception("No tiene permisos para realizar esta reserva");
        }

        return true;
    }

    private function validarFechaHora($reserva) {
        $fechaActual = new DateTime();
        $fechaReserva = new DateTime($reserva->getFecha() . ' ' . $reserva->getHoraInicio());
        
        if ($fechaReserva < $fechaActual) {
            return false;
        }

        $horaInicio = new DateTime($reserva->getHoraInicio());
        $horaFin = new DateTime($reserva->getHoraFin());
        
        return $horaFin > $horaInicio;
    }

    private function validarFormatoFechaHora($reserva) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $reserva->getFecha())) {
            return false;
        }

        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $reserva->getHoraInicio()) ||
            !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $reserva->getHoraFin())) {
            return false;
        }

        return true;
    }

    private function validarTipoVehiculo($reserva) {
        try {
            $query = $this->conexion->prepare("
                SELECT COUNT(*) as count 
                FROM INFO1170_Zonas 
                WHERE id = ? 
                AND tipos_vehiculos_permitidos LIKE ?
            ");
            
            $tipoVehiculoPattern = "%" . $reserva->getTipoVehiculo() . "%";
            $query->bind_param("is", $reserva->getZona(), $tipoVehiculoPattern);
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            
            return $result['count'] > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function validarCapacidad($reserva) {
        try {
            $query = $this->conexion->prepare("
                SELECT COUNT(*) as reservas_actuales 
                FROM INFO1170_Reservas 
                WHERE zona = ? 
                AND fecha = ? 
                AND (
                    (hora_inicio <= ? AND hora_fin > ?) OR
                    (hora_inicio < ? AND hora_fin >= ?) OR
                    (hora_inicio >= ? AND hora_fin <= ?)
                )
            ");
            
            $query->bind_param("sssssssss", 
                $reserva->getZona(), 
                $reserva->getFecha(), 
                $reserva->getHoraInicio(), $reserva->getHoraInicio(),
                $reserva->getHoraFin(), $reserva->getHoraFin(),
                $reserva->getHoraInicio(), $reserva->getHoraFin()
            );
            
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            
            return $result['reservas_actuales'] < $reserva->getCapacidadMaxima();
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function validarPermisos($reserva) {
        try {
            $query = $this->conexion->prepare("
                SELECT COUNT(*) as count 
                FROM INFO1170_Usuarios 
                WHERE id = ? 
                AND estado = 'activo'
                AND (
                    SELECT COUNT(*) 
                    FROM INFO1170_Reservas 
                    WHERE usuario_id = ? 
                    AND fecha = ?
                ) < 3
            ");
            
            $query->bind_param("iis", $reserva->getUsuarioId(), $reserva->getUsuarioId(), $reserva->getFecha());
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            
            return $result['count'] > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

class Reserva implements IReserva {
    private $id;
    private $evento;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $zona;
    private $conexion;
    private $subject;
    private $tipoVehiculo;
    private $usuarioId;
    private $capacidadMaxima;
    private $validator;

    public function __construct($datos = []) {
        $this->conexion = new Conexion();
        $this->subject = new ReservaSubject();
        $this->validator = new ReservaValidator($this->conexion);
        
        if (!empty($datos)) {
            $this->id = $datos['id'] ?? null;
            $this->evento = $datos['evento'] ?? '';
            $this->fecha = $datos['fecha'] ?? '';
            $this->horaInicio = $datos['horaInicio'] ?? '';
            $this->horaFin = $datos['horaFin'] ?? '';
            $this->zona = $datos['zona'] ?? '';
            $this->tipoVehiculo = $datos['tipoVehiculo'] ?? '';
            $this->usuarioId = $datos['usuarioId'] ?? null;
            $this->capacidadMaxima = $datos['capacidadMaxima'] ?? 1;
        }
    }

    // Métodos para gestionar observadores
    public function attachObserver(ReservaObserver $observer) {
        $this->subject->attach($observer);
    }

    public function detachObserver(ReservaObserver $observer) {
        $this->subject->detach($observer);
    }

    // Getters y Setters
    public function getId() { return $this->id; }
    public function getEvento() { return $this->evento; }
    public function getFecha() { return $this->fecha; }
    public function getHoraInicio() { return $this->horaInicio; }
    public function getHoraFin() { return $this->horaFin; }
    public function getZona() { return $this->zona; }
    public function getTipoVehiculo() { return $this->tipoVehiculo; }
    public function getUsuarioId() { return $this->usuarioId; }
    public function getCapacidadMaxima() { return $this->capacidadMaxima; }

    public function setEvento($evento) { $this->evento = $evento; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setHoraInicio($horaInicio) { $this->horaInicio = $horaInicio; }
    public function setHoraFin($horaFin) { $this->horaFin = $horaFin; }
    public function setZona($zona) { $this->zona = $zona; }
    public function setTipoVehiculo($tipoVehiculo) { $this->tipoVehiculo = $tipoVehiculo; }
    public function setUsuarioId($usuarioId) { $this->usuarioId = $usuarioId; }
    public function setCapacidadMaxima($capacidadMaxima) { $this->capacidadMaxima = $capacidadMaxima; }

    // Método para validar datos
    private function validarDatos() {
        try {
            $this->validator->validarDatos($this);
            return true;
        } catch (Exception $e) {
            $this->subject->notify("Error al validar datos: " . $e->getMessage());
            throw $e;
        }
    }

    // Implementación de métodos de la interfaz
    public function crearReserva() {
        try {
            $this->validarDatos();
            
            if (!$this->verificarDisponibilidad()) {
                throw new Exception("La zona no está disponible en el horario seleccionado");
            }

            $query = $this->conexion->prepare("INSERT INTO INFO1170_Reservas (evento, fecha, hora_inicio, hora_fin, zona) VALUES (?, ?, ?, ?, ?)");
            $query->bind_param("sssss", $this->evento, $this->fecha, $this->horaInicio, $this->horaFin, $this->zona);
            
            if ($query->execute()) {
                $this->subject->notify("Nueva reserva creada: " . $this->evento);
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->subject->notify("Error al crear reserva: " . $e->getMessage());
            throw $e;
        }
    }

    public function actualizarReserva() {
        try {
            $this->validarDatos();
            
            if (!$this->verificarDisponibilidad()) {
                throw new Exception("La zona no está disponible en el horario seleccionado");
            }

            $query = $this->conexion->prepare("UPDATE INFO1170_Reservas SET evento = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, zona = ? WHERE id = ?");
            $query->bind_param("sssssi", $this->evento, $this->fecha, $this->horaInicio, $this->horaFin, $this->zona, $this->id);
            
            if ($query->execute()) {
                $this->subject->notify("Reserva actualizada: " . $this->evento);
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->subject->notify("Error al actualizar reserva: " . $e->getMessage());
            throw $e;
        }
    }

    public function eliminarReserva() {
        try {
            $query = $this->conexion->prepare("DELETE FROM INFO1170_Reservas WHERE id = ?");
            $query->bind_param("i", $this->id);
            
            if ($query->execute()) {
                $this->subject->notify("Reserva eliminada: " . $this->evento);
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->subject->notify("Error al eliminar reserva: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerReservaPorId() {
        try {
            $query = $this->conexion->prepare("SELECT * FROM INFO1170_Reservas WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            return $query->get_result()->fetch_assoc();
        } catch (Exception $e) {
            $this->subject->notify("Error al obtener reserva: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerTodasLasReservas() {
        try {
            $query = $this->conexion->prepare("SELECT * FROM INFO1170_Reservas ORDER BY fecha, hora_inicio");
            $query->execute();
            return $query->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $this->subject->notify("Error al obtener reservas: " . $e->getMessage());
            throw $e;
        }
    }

    public function verificarDisponibilidad() {
        try {
            $query = $this->conexion->prepare("
                SELECT COUNT(*) as count 
                FROM INFO1170_Reservas 
                WHERE zona = ? 
                AND fecha = ? 
                AND (
                    (hora_inicio <= ? AND hora_fin > ?) OR
                    (hora_inicio < ? AND hora_fin >= ?) OR
                    (hora_inicio >= ? AND hora_fin <= ?)
                )
            ");
            
            $query->bind_param("sssssssss", 
                $this->zona, 
                $this->fecha, 
                $this->horaInicio, $this->horaInicio,
                $this->horaFin, $this->horaFin,
                $this->horaInicio, $this->horaFin
            );
            
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            
            return $result['count'] == 0;
        } catch (Exception $e) {
            $this->subject->notify("Error al verificar disponibilidad: " . $e->getMessage());
            throw $e;
        }
    }
}
?>

