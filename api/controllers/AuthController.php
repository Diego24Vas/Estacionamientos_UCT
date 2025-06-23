<?php
require_once './config/database.php';

class AuthController {
    public function login() {
        // Obtener los datos del POST
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Validación básica
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos"]);
            return;
        }

        // Conexión a la BD
        $conn = Database::getConnection();
        $query = "SELECT * FROM INFO1170_RegistroUsuarios WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['contraseña'] === $password) {
            // Éxito
            echo json_encode([
                "mensaje" => "Login exitoso",
                "usuario" => [
                    "id" => $usuario['id'],
                    "nombre" => $usuario['nombre'],
                    "email" => $usuario['email']
                ]
            ]);
        } else {
            // Fallo
            http_response_code(401);
            echo json_encode(["mensaje" => "Credenciales incorrectas"]);
        }
    }
}
