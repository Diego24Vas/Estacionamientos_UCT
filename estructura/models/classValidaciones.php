<?php
class Validaciones {
    public static function camposVacios($campos) {
        foreach ($campos as $campo) {
            if (empty(trim($campo))) {
                return ["status" => "error", "message" => "Todos los campos son obligatorios."];
            }
        }
        return ["status" => "ok"];
    }

    public static function validarEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo no es válido."];
        }
        return ["status" => "ok"];
    }

    public static function validarLongitud($texto, $min, $max) {
        $longitud = strlen($texto);
        if ($longitud < $min || $longitud > $max) {
            return ["status" => "error", "message" => "La longitud debe estar entre $min y $max caracteres."];
        }
        return ["status" => "ok"];
    }

    public static function validarPatente($patente) {
        // Ejemplo: formato chileno tipo ABCD12 o AB1234
        if (!preg_match('/^[A-Z]{2,4}[0-9]{2,4}$/', strtoupper($patente))) {
            return ["status" => "error", "message" => "Formato de patente inválido."];
        }
        return ["status" => "ok"];
    }
}
?>
