<?php
require_once './models/Usuario.php';

class UsuarioController {
    public function listar() {
        $usuarios = Usuario::obtenerTodos();
        echo json_encode($usuarios);
    }

    public function crear() {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!isset($data['nombre'], $data['email'], $data['contraseña'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos"]);
            return;
        }

        $resultado = Usuario::crear($data['nombre'], $data['email'], $data['contraseña']);

        if ($resultado) {
            echo json_encode(["mensaje" => "Usuario creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear usuario"]);
        }
    }
}
