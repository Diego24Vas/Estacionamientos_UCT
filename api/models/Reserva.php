<?php
require_once './config/database.php';

class Reserva {
    public static function crear($usuario_id, $fecha, $hora_inicio, $hora_fin, $estacionamiento_id, $estado) {
        $conn = Database::getConnection();
        $query = "INSERT INTO INFO1170_Reservas (usuario_id, fecha, hora_inicio, hora_fin, estacionamiento_id, estado)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$usuario_id, $fecha, $hora_inicio, $hora_fin, $estacionamiento_id, $estado]);
    }

    public static function obtenerPorUsuario($usuario_id) {
        $conn = Database::getConnection();
        $query = "SELECT * FROM INFO1170_Reservas WHERE usuario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
