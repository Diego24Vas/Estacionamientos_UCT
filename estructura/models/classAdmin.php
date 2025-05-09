<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once CONFIG_PATH . '/conex.php';

class Administrador {
    // Propiedades
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $conexion;
    private $privilegios;  // Nuevo atributo para privilegios

    // Constructor
    public function __construct($id, $nombre, $email, $password, $conexion, $privilegios = 'usuario') {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->conexion = $conexion;
        $this->privilegios = $privilegios;  // Inicializa el atributo privilegios
    }

    // Metodo para registrar un nuevo administrador
    public function registrarAdmin($nombre, $email, $password, $privilegios = 'usuario') {
        // Validación de los datos
        if (empty($nombre) || empty($email) || empty($password)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo proporcionado no es válido."];
        }

        // Verificar si el usuario o correo ya existen
        $check_stmt = $this->conexion->prepare("SELECT id FROM INFO1170_RegistroAdministradores WHERE nombre = ? OR email = ?");
        $check_stmt->bind_param("ss", $nombre, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->close();
            return ["status" => "error", "message" => "El usuario o correo ya está registrado."];
        }

        $check_stmt->close();

        // Hashear la contraseña antes de guardarla
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar el nuevo administrador en la base de datos
        $stmt = $this->conexion->prepare("INSERT INTO INFO1170_RegistroAdministradores (nombre, email, contraseña, privilegios) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $hashed_password, $privilegios);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Registro exitoso"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al registrar."];
        }
    }

    // Método para iniciar sesión
    public function iniciarSesion($nombre, $password) {
        // Validación básica
        if (empty($nombre) || empty($password)) {
            return ["status" => "error", "message" => "Por favor, completa todos los campos."];
        }

        // Preparar consulta para verificar usuario
        $stmt = $this->conexion->prepare("SELECT contraseña, privilegios FROM INFO1170_RegistroAdministradores WHERE nombre = ?");
        if ($stmt === false) {
            return ["status" => "error", "message" => "Error en la consulta."];
        }

        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt->close();
            return ["status" => "error", "message" => "Usuario no encontrado."];
        }

        // Declarar las variables antes de bind_result
        $hashed_password = "";
        $privilegios = "";

        // Ahora vinculamos las variables
        $stmt->bind_result($hashed_password, $privilegios);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['nombre'] = $nombre;
            $_SESSION['privilegios'] = $privilegios;  // Guardar los privilegios en la sesión
            return ["status" => "success", "message" => "Inicio de sesión exitoso"];
        } else {
            return ["status" => "error", "message" => "Contraseña incorrecta."];
        }

    }

    // Método para obtener los privilegios del administrador
    public function getPrivilegios() {
        return $this->privilegios;
    }

    // Método para establecer los privilegios del administrador
    public function setPrivilegios($privilegios) {
        $this->privilegios = $privilegios;
    }

    // Método para actualizar los privilegios en la base de datos
    public function actualizarPrivilegios($id, $privilegios) {
        $stmt = $this->conexion->prepare("UPDATE INFO1170_RegistroAdministradores SET privilegios = ? WHERE id = ?");
        $stmt->bind_param("si", $privilegios, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Privilegios actualizados"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al actualizar los privilegios."];
        }
    }
}

?>
