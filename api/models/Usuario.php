<?php
require_once './config/database.php';

class Usuario {
    public static function obtenerTodos() {
        $conn = Database::getConnection();
        $stmt = $conn->query("SELECT id, nombre, email FROM INFO1170_RegistroUsuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($nombre, $email, $contraseña) {
        $conn = Database::getConnection();
        $query = "INSERT INTO INFO1170_RegistroUsuarios (nombre, email, contraseña) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$nombre, $email, $contraseña]);
    }
}
