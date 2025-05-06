<?php
require_once dirname(__DIR__) . '/config/config.php';
include('conex.php');

class Vehiculo {
    private $id;
    private $marca;
    private $modelo;
    private $patente;
    private $conexion;

    public function __construct($conexion, $id = null, $marca = null, $modelo = null, $patente = null) {
        $this->conexion = $conexion;
        $this->id = $id;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->patente = $patente;
    }

    public function registrarVehiculo($marca, $modelo, $patente) {
        if (empty($marca) || empty($modelo) || empty($patente)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        // Verificar si la patente ya existe
        $check = $this->conexion->prepare("SELECT id FROM Vehiculos WHERE patente = ?");
        $check->bind_param("s", $patente);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->close();
            return ["status" => "error", "message" => "La patente ya está registrada."];
        }

        $check->close();

        // Insertar nuevo vehículo
        $stmt = $this->conexion->prepare("INSERT INTO Vehiculos (marca, modelo, patente) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $marca, $modelo, $patente);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Vehículo registrado con éxito."];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al registrar vehículo."];
        }
    }

    public function obtenerVehiculoPorPatente($patente) {
        $stmt = $this->conexion->prepare("SELECT id, marca, modelo, patente FROM Vehiculos WHERE patente = ?");
        $stmt->bind_param("s", $patente);
        $stmt->execute();
        $stmt->bind_result($id, $marca, $modelo, $patente);
        
        if ($stmt->fetch()) {
            $stmt->close();
            return ["id" => $id, "marca" => $marca, "modelo" => $modelo, "patente" => $patente];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Vehículo no encontrado."];
        }
    }
}
?>