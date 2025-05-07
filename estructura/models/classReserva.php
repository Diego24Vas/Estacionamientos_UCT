<?php
require_once dirname(__DIR__) . '/config/config.php';
include('conex.php');

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

class Reserva implements IReserva {
    private $id;
    private $evento;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $zona;
    private $conexion;
    private $subject;

    public function __construct($datos = []) {
        $this->conexion = new Conexion();
        $this->subject = new ReservaSubject();
        
        if (!empty($datos)) {
            $this->id = $datos['id'] ?? null;
            $this->evento = $datos['evento'] ?? '';
            $this->fecha = $datos['fecha'] ?? '';
            $this->horaInicio = $datos['horaInicio'] ?? '';
            $this->horaFin = $datos['horaFin'] ?? '';
            $this->zona = $datos['zona'] ?? '';
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

    public function setEvento($evento) { $this->evento = $evento; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setHoraInicio($horaInicio) { $this->horaInicio = $horaInicio; }
    public function setHoraFin($horaFin) { $this->horaFin = $horaFin; }
    public function setZona($zona) { $this->zona = $zona; }

    // Método para validar datos
    private function validarDatos() {
        if (empty($this->evento) || empty($this->fecha) || empty($this->horaInicio) || 
            empty($this->horaFin) || empty($this->zona)) {
            throw new Exception("Todos los campos son obligatorios");
        }

        if (!$this->validarFechaHora()) {
            throw new Exception("La fecha y hora no son válidas");
        }
    }

    // Método para validar fecha y hora
    private function validarFechaHora() {
        $fechaActual = new DateTime();
        $fechaReserva = new DateTime($this->fecha . ' ' . $this->horaInicio);
        
        if ($fechaReserva < $fechaActual) {
            return false;
        }

        $horaInicio = new DateTime($this->horaInicio);
        $horaFin = new DateTime($this->horaFin);
        
        return $horaFin > $horaInicio;
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

