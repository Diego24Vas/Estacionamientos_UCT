<?php
class Usuario {
    private $id;
    private $nombre;
    private $email;
    private $contraseña;
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function registrar($nombre_usuario, $correo, $contrasena) {
        // Validación de los datos
        if (empty($nombre_usuario) || empty($correo) || empty($contrasena)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo proporcionado no es válido."];
        }

        // Verificar si el usuario o correo ya existen
        $check_stmt = $this->conexion->prepare("SELECT id FROM INFO1170_RegistroUsuarios WHERE nombre = ? OR email = ?");
        $check_stmt->bind_param("ss", $nombre_usuario, $correo);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->close();
            return ["status" => "error", "message" => "El usuario o correo ya está registrado."];
        }

        $check_stmt->close();

        // Hashear la contraseña antes de guardarla
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario en la base de datos
        $stmt = $this->conexion->prepare("INSERT INTO INFO1170_RegistroUsuarios (nombre, email, contraseña) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre_usuario, $correo, $hashed_password);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Registro exitoso"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al registrar."];
        }
    }

    public function iniciarSesion($username, $password) {
        // Validación básica
        if (empty($username) || empty($password)) {
            return ["status" => "error", "message" => "Por favor, completa todos los campos."];
        }

        // Preparar consulta para verificar usuario
        $stmt = $this->conexion->prepare("SELECT contraseña FROM INFO1170_RegistroUsuarios WHERE nombre = ?");
        if ($stmt === false) {
            return ["status" => "error", "message" => "Error en la preparación de la consulta: " . $this->conexion->error];
        }        

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verificar si el usuario existe y la contraseña es correcta
        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            session_start();
            $_SESSION['usuario'] = $username;
            $stmt->close();
            return ["status" => "success", "message" => "Inicio de sesión exitoso"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
        }
    }
}
?>
