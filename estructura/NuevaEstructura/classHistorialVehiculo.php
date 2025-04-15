<?php
include('conex.php');

class HistorialVehiculo {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function agregarEvento($idVehiculo, $descripcion, $fecha) {
        if (empty($idVehiculo) || empty($descripcion) || empty($fecha)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        $stmt = $this->conexion->prepare("INSERT INTO HistorialVehiculo (id_vehiculo, descripcion, fecha) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $idVehiculo, $descripcion, $fecha);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Evento agregado al historial."];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al agregar evento al historial."];
        }
    }

    public function obtenerHistorialPorVehiculo($idVehiculo) {
        $stmt = $this->conexion->prepare("SELECT descripcion, fecha FROM HistorialVehiculo WHERE id_vehiculo = ? ORDER BY fecha DESC");
        $stmt->bind_param("i", $idVehiculo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $historial = [];
        while ($row = $resultado->fetch_assoc()) {
            $historial[] = $row;
        }

        $stmt->close();
        return $historial;
    }
}
?>
