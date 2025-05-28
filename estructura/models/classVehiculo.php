<?php
// Interfaz para los vehículos
interface IVehiculo {
    public function obtenerTipo();
    public function registrarVehiculo();
}

// Clase abstracta base para todos los vehículos
abstract class Vehiculo implements IVehiculo {
    protected $conexion;
    protected $marca;
    protected $modelo;
    protected $anio;
    protected $tipo;

    public function __construct($conexion, $datos = []) {
        $this->conexion = $conexion;
        $this->marca = $datos['marca'] ?? '';
        $this->modelo = $datos['modelo'] ?? '';
        $this->anio = $datos['anio'] ?? '';
        $this->tipo = $datos['tipo'] ?? 'generico';
    }

    public function obtenerTipo() {
        return $this->tipo;
    }

    public function registrarVehiculo() {
        $stmt = $this->conexion->prepare("INSERT INTO INFO1170_Vehiculos (marca, modelo, anio, tipo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $this->marca, $this->modelo, $this->anio, $this->tipo);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Vehículo registrado correctamente"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al registrar el vehículo"];
        }
    }
}

// Clase concreta Auto
class Auto extends Vehiculo {
    public function __construct($conexion, $datos = []) {
        parent::__construct($conexion, $datos);
        $this->tipo = 'auto';
    }
}

// Clase concreta Moto
class Moto extends Vehiculo {
    public function __construct($conexion, $datos = []) {
        parent::__construct($conexion, $datos);
        $this->tipo = 'moto';
    }
}
?>
