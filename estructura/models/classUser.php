<?php
require_once dirname(__DIR__) . '/config/config.php';

// Interfaz para el patrón Factory Method
interface IUsuario {
    public function registrar($nombre_usuario, $correo, $contrasena);
    public function iniciarSesion($username, $password);
}

// Clase abstracta para el patrón Factory Method
abstract class UsuarioFactory {
    abstract public function crearUsuario($conexion, $datos = []);
}

// Implementación concreta del Factory para usuarios regulares
class UsuarioRegularFactory extends UsuarioFactory {
    public function crearUsuario($conexion, $datos = []) {
        return new Usuario($conexion, $datos);
    }
}

// Implementación concreta del Factory para usuarios administradores
class UsuarioAdminFactory extends UsuarioFactory {
    public function crearUsuario($conexion, $datos = []) {
        return new UsuarioAdmin($conexion, $datos);
    }
}

class Usuario implements IUsuario {
    private $id;
    private $nombre;
    private $email;
    private $contraseña;
    private $conexion;
    private $tipo;

    public function __construct($conexion, $datos = []) {
        $this->conexion = $conexion;
        
        if (!empty($datos)) {
            $this->id = $datos['id'] ?? null;
            $this->nombre = $datos['nombre'] ?? '';
            $this->email = $datos['email'] ?? '';
            $this->contraseña = $datos['contraseña'] ?? '';
            $this->tipo = $datos['tipo'] ?? 'regular';
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getTipo() { return $this->tipo; }

    // Setters
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setEmail($email) { $this->email = $email; }
    public function setTipo($tipo) { $this->tipo = $tipo; }

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

// Clase para usuarios administradores
class UsuarioAdmin extends Usuario {
    private $privilegios;

    public function __construct($conexion, $datos = []) {
        parent::__construct($conexion, $datos);
        $this->privilegios = $datos['privilegios'] ?? 'admin';
    }

    public function getPrivilegios() { return $this->privilegios; }
    public function setPrivilegios($privilegios) { $this->privilegios = $privilegios; }

    public function registrar($nombre_usuario, $correo, $contrasena) {
        // Validación de los datos
        if (empty($nombre_usuario) || empty($correo) || empty($contrasena)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo proporcionado no es válido."];
        }

        // Verificar si el usuario o correo ya existen
        $check_stmt = $this->conexion->prepare("SELECT id FROM INFO1170_RegistroAdministradores WHERE nombre = ? OR email = ?");
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

        // Insertar el nuevo administrador en la base de datos
        $stmt = $this->conexion->prepare("INSERT INTO INFO1170_RegistroAdministradores (nombre, email, contraseña, privilegios) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre_usuario, $correo, $hashed_password, $this->privilegios);

        if ($stmt->execute()) {
            $stmt->close();
            return ["status" => "success", "message" => "Registro de administrador exitoso"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Error al registrar administrador."];
        }
    }
}
?>
