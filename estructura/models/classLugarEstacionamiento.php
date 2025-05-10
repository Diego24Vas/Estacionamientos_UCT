<?php
include('conex.php');

class LugarEstacionamiento {
    private $id;
    private $ubicacion;
    private $estado; // libre u ocupado
    private $vehiculo_id; // null si está libre
    private $conexion;

    public function __construct($conexion, $id = null, $ubicacion = null, $estado = "libre", $vehiculo_id = null) {
        $this->conexion = $conexion;
        $this->id = $id;
        $this->ubicacion = $ubicacion;
        $this->estado = $estado;
        $this->vehiculo_id = $vehiculo_id;
    }

    public function crearLugar($ubicacion) {
        if (empty($ubicacion)) {
            return ["status" => "error", "message" => "Debe indicar la ubicación del lugar."];
        }

        $stmt = $this->conexion->prepare("INSERT INTO LugaresEstacionamiento (ubicacion, estado) VALUES (?, 'libre')");
        $stmt->bind_param("s", $ubicacion);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Lugar de estacionamiento creado."];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al crear lugar."];
        }
    }

    public function ocuparLugar($idLugar, $vehiculo_id) {
        $stmt = $this->conexion->prepare("UPDATE LugaresEstacionamiento SET estado = 'ocupado', vehiculo_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $vehiculo_id, $idLugar);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Lugar ocupado por vehículo $vehiculo_id"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al ocupar el lugar."];
        }
    }

    public function liberarLugar($idLugar) {
        $stmt = $this->conexion->prepare("UPDATE LugaresEstacionamiento SET estado = 'libre', vehiculo_id = NULL WHERE id = ?");
        $stmt->bind_param("i", $idLugar);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Lugar liberado"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al liberar lugar."];
        }
    }

    public function listarLugares() {
        $stmt = $this->conexion->prepare("SELECT id, ubicacion, estado, vehiculo_id FROM LugaresEstacionamiento ORDER BY id ASC");
        $stmt->execute();
        $resultado = $stmt->get_result();

        $lugares = [];
        while ($row = $resultado->fetch_assoc()) {
            $lugares[] = $row;
        }

        $stmt->close();
        return $lugares;
    }
}
?>
