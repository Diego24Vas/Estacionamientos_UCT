<?php
require_once './models/Reserva.php';

class ReservaController {
    public function crear() {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!isset($data['usuario_id'], $data['fecha'], $data['hora_inicio'], $data['hora_fin'], $data['estacionamiento_id'], $data['estado'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos para crear la reserva"]);
            return;
        }

        $resultado = Reserva::crear(
            $data['usuario_id'],
            $data['fecha'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['estacionamiento_id'],
            $data['estado']
        );

        if ($resultado) {
            echo json_encode(["mensaje" => "Reserva creada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear la reserva"]);
        }
    }

    public function listarPorUsuario() {
        if (!isset($_GET['usuario_id'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta usuario_id en la consulta"]);
            return;
        }

        $usuario_id = $_GET['usuario_id'];
        $reservas = Reserva::obtenerPorUsuario($usuario_id);
        echo json_encode($reservas);
    }
}
