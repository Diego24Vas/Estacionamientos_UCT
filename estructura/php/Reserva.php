<?php
class Reserva {
    private $conexion;
    private $id;
    private $evento;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $zona;
    
    // Constructor
    public function __construct($id = null, $evento = '', $fecha = '', $horaInicio = '', $horaFin = '', $zona = '') {
        $this->conexion = new Conexion(); 
        $this->id = $id;
        $this->evento = $evento;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->zona = $zona;
    }
    
    // Metodo para crear una nueva reserva
    public function crearReserva() {
        $query = $this->conexion->prepare("INSERT INTO INFO1170_Reservas (evento, fecha, hora_inicio, hora_fin, zona) VALUES (?, ?, ?, ?, ?)");
        $query->bind_param("sssss", $this->evento, $this->fecha, $this->horaInicio, $this->horaFin, $this->zona);
        return $query->execute();
    }

    // Metodo para actualizar una reserva existente
    public function actualizarReserva() {
        $query = $this->conexion->prepare("UPDATE INFO1170_Reservas SET evento = ?, fecha = ?, hora_inicio = ?, hora_fin = ?, zona = ? WHERE id = ?");
        $query->bind_param("sssssi", $this->evento, $this->fecha, $this->horaInicio, $this->horaFin, $this->zona, $this->id);
        return $query->execute();
    }

    // Metodo para eliminar una reserva
    public function eliminarReserva() {
        $query = $this->conexion->prepare("DELETE FROM INFO1170_Reservas WHERE id = ?");
        $query->bind_param("i", $this->id);
        return $query->execute();
    }
 
    // Metodo para obtener una reserva por ID
    public function obtenerReservaPorId() {
        $query = $this->conexion->prepare("SELECT * FROM INFO1170_Reservas WHERE id = ?");
        $query->bind_param("i", $this->id);
        $query->execute();
        return $query->get_result()->fetch_assoc();
    }
}
?>
