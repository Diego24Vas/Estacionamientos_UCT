<?php
require_once '../interfaces/EspacioObserver.php';

class EspacioEstacionamiento {
    private $conexion;
    private $observers = [];

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtener todos los espacios y su estado
    public function listarEspacios() {
        $query = "SELECT IdEstacionamiento, Ubicacion, Estado FROM INFO1170_Estacionamiento";
        $result = $this->conexion->query($query);
        $espacios = [];
        while ($row = $result->fetch_assoc()) {
            $espacios[] = $row;
        }
        return $espacios;
    }

    // Obtener espacios disponibles por zona
    public function espaciosDisponiblesPorZona($zona) {
        $query = "SELECT IdEstacionamiento FROM INFO1170_Estacionamiento WHERE Ubicacion = ? AND Estado = 'Disponible'";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $zona);
        $stmt->execute();
        $result = $stmt->get_result();
        $espacios = [];
        while ($row = $result->fetch_assoc()) {
            $espacios[] = $row['IdEstacionamiento'];
        }
        return $espacios;
    }

    // Suscribir un observer
    public function agregarObserver(EspacioObserver $observer) {
        $this->observers[] = $observer;
    }

    // Notificar a todos los observers
    private function notificarObservers($idEspacio, $nuevoEstado) {
        foreach ($this->observers as $observer) {
            $observer->actualizar($idEspacio, $nuevoEstado);
        }
    }

    // Asignar espacio (ocupar)
    public function ocuparEspacio($idEspacio) {
        $query = "UPDATE INFO1170_Estacionamiento SET Estado = 'Ocupado' WHERE IdEstacionamiento = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $idEspacio);
        $resultado = $stmt->execute();
        if ($resultado) {
            $this->notificarObservers($idEspacio, 'Ocupado');
        }
        return $resultado;
    }

    // Liberar espacio
    public function liberarEspacio($idEspacio) {
        $query = "UPDATE INFO1170_Estacionamiento SET Estado = 'Disponible' WHERE IdEstacionamiento = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $idEspacio);
        $resultado = $stmt->execute();
        if ($resultado) {
            $this->notificarObservers($idEspacio, 'Disponible');
        }
        return $resultado;
    }

    // Verificar si un espacio está disponible
    public function estaDisponible($idEspacio) {
        $query = "SELECT Estado FROM INFO1170_Estacionamiento WHERE IdEstacionamiento = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $idEspacio);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result && $result['Estado'] === 'Disponible';
    }

    // Obtener el primer espacio disponible
    public function obtenerPrimerEspacioDisponible() {
        $query = "SELECT IdEstacionamiento FROM INFO1170_Estacionamiento WHERE Estado = 'Disponible' LIMIT 1";
        $result = $this->conexion->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['IdEstacionamiento'];
        }
        return null;
    }

    // Obtener estadísticas de espacios
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN Estado = 'Disponible' THEN 1 ELSE 0 END) AS disponibles,
                    SUM(CASE WHEN Estado = 'Ocupado' THEN 1 ELSE 0 END) AS ocupados
                  FROM INFO1170_Estacionamiento";
        $result = $this->conexion->query($query);
        return $result ? $result->fetch_assoc() : null;
    }

    // Verificar si hay espacios disponibles
    public function hayEspaciosDisponibles() {
        $query = "SELECT COUNT(*) AS count FROM INFO1170_Estacionamiento WHERE Estado = 'Disponible'";
        $result = $this->conexion->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        }
        return false;
    }

    // Obtener información del vehículo en un espacio específico
    public function obtenerVehiculoEnEspacio($idEspacio) {
        $query = "SELECT vr.id, vr.nombre, vr.apellido, vr.patente 
                  FROM INFO1170_VehiculosRegistrados vr 
                  WHERE vr.espacio_estacionamiento = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $idEspacio);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
}
?>