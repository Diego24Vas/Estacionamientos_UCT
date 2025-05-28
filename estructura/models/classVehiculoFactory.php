<?php
require_once 'classVehiculo.php';

// Clase abstracta del Factory Method
abstract class VehiculoFactory {
    abstract public function crearVehiculo($conexion, $datos = []): IVehiculo;
}

// Fábrica concreta para Autos
class AutoFactory extends VehiculoFactory {
    public function crearVehiculo($conexion, $datos = []): IVehiculo {
        return new Auto($conexion, $datos);
    }
}

// Fábrica concreta para Motos
class MotoFactory extends VehiculoFactory {
    public function crearVehiculo($conexion, $datos = []): IVehiculo {
        return new Moto($conexion, $datos);
    }
}

// Clase auxiliar para seleccionar la fábrica adecuada
class VehiculoFactorySelector {
    public static function obtenerFactory(string $tipo): VehiculoFactory {
        switch (strtolower($tipo)) {
            case 'auto':
                return new AutoFactory();
            case 'moto':
                return new MotoFactory();
            default:
                throw new Exception("Tipo de vehículo no soportado: $tipo");
        }
    }
}
?>
