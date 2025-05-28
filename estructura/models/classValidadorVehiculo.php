<?php
class ValidadorVehiculo {
    public static function validar(array $datos): array {
        $errores = [];

        if (empty($datos['marca'])) {
            $errores[] = "La marca es obligatoria.";
        }

        if (empty($datos['modelo'])) {
            $errores[] = "El modelo es obligatorio.";
        }

        if (empty($datos['anio']) || !is_numeric($datos['anio']) || $datos['anio'] < 1900 || $datos['anio'] > date("Y")) {
            $errores[] = "El año es inválido.";
        }

        if (!isset($datos['tipo']) || !in_array(strtolower($datos['tipo']), ['auto', 'moto'])) {
            $errores[] = "Tipo de vehículo no permitido.";
        }

        // Validación de patente 
        if (!empty($datos['patente'])) {
            $patente = strtoupper(trim($datos['patente']));
            if (!preg_match('/^[A-Z]{2}[0-9]{3}[A-Z]{2}$/', $patente) && !preg_match('/^[A-Z]{3}[0-9]{3}$/', $patente)) {
                $errores[] = "La patente no tiene un formato válido.";
            }
        } else {
            $errores[] = "La patente es obligatoria.";
        }

        return $errores;
    }
}
?>
