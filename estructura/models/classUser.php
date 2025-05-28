<?php
require_once dirname(__DIR__) . '/config/config.php';

// Patrón Strategy para validaciones
interface IValidationStrategy {
    public function validate($data): array;
}

class BasicUserValidationStrategy implements IValidationStrategy {
    public function validate($data): array {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors[] = "El nombre es obligatorio.";
        }
        
        if (empty($data['email'])) {
            $errors[] = "El email es obligatorio.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El correo proporcionado no es válido.";
        }
        
        if (empty($data['password'])) {
            $errors[] = "La contraseña es obligatoria.";
        } elseif (strlen($data['password']) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres.";
        }
        
        return $errors;
    }
}

class AdminUserValidationStrategy implements IValidationStrategy {
    public function validate($data): array {
        $basicStrategy = new BasicUserValidationStrategy();
        $errors = $basicStrategy->validate($data);
        
        // Validaciones adicionales para administradores
        if (strlen($data['password'] ?? '') < 8) {
            $errors[] = "La contraseña de administrador debe tener al menos 8 caracteres.";
        }
        
        if (!preg_match('/[A-Z]/', $data['password'] ?? '')) {
            $errors[] = "La contraseña debe contener al menos una letra mayúscula.";
        }
        
        if (!preg_match('/[0-9]/', $data['password'] ?? '')) {
            $errors[] = "La contraseña debe contener al menos un número.";
        }
        
        return $errors;
    }
}

// Contexto para el patrón Strategy
class UserValidator {
    private $strategy;
    
    public function __construct(IValidationStrategy $strategy) {
        $this->strategy = $strategy;
    }
    
    public function setStrategy(IValidationStrategy $strategy) {
        $this->strategy = $strategy;
    }
    
    public function validate($data): array {
        return $this->strategy->validate($data);
    }
}

// Patrón Observer para notificaciones de usuario
interface IUserObserver {
    public function onUserRegistered($userData): void;
    public function onUserLogin($userData): void;
    public function onUserLogout($userData): void;
}

class EmailNotificationObserver implements IUserObserver {
    public function onUserRegistered($userData): void {
        // Simular envío de email de bienvenida
        error_log("Email de bienvenida enviado a: " . $userData['email']);
    }
    
    public function onUserLogin($userData): void {
        // Registrar inicio de sesión
        error_log("Usuario logueado: " . $userData['nombre']);
    }
    
    public function onUserLogout($userData): void {
        // Registrar cierre de sesión
        error_log("Usuario deslogueado: " . $userData['nombre']);
    }
}

class AuditLogObserver implements IUserObserver {
    public function onUserRegistered($userData): void {
        error_log("AUDIT: Nuevo usuario registrado - ID: " . ($userData['id'] ?? 'N/A'));
    }
    
    public function onUserLogin($userData): void {
        error_log("AUDIT: Login exitoso - Usuario: " . $userData['nombre']);
    }
    
    public function onUserLogout($userData): void {
        error_log("AUDIT: Logout - Usuario: " . $userData['nombre']);
    }
}

// Subject para el patrón Observer
class UserEventSubject {
    private $observers = [];
    
    public function attach(IUserObserver $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(IUserObserver $observer): void {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }
    
    public function notifyUserRegistered($userData): void {
        foreach ($this->observers as $observer) {
            $observer->onUserRegistered($userData);
        }
    }
    
    public function notifyUserLogin($userData): void {
        foreach ($this->observers as $observer) {
            $observer->onUserLogin($userData);
        }
    }
    
    public function notifyUserLogout($userData): void {
        foreach ($this->observers as $observer) {
            $observer->onUserLogout($userData);
        }
    }
}

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
    private $validator;
    private $eventSubject;

    public function __construct($conexion, $datos = [], UserValidator $validator = null, UserEventSubject $eventSubject = null) {
        $this->conexion = $conexion;
        $this->validator = $validator ?? new UserValidator(new BasicUserValidationStrategy());
        $this->eventSubject = $eventSubject ?? new UserEventSubject();
        
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
        // Usar Strategy para validación
        $data = [
            'nombre' => $nombre_usuario,
            'email' => $correo,
            'password' => $contrasena
        ];
        
        $errors = $this->validator->validate($data);
        if (!empty($errors)) {
            return ["status" => "error", "message" => implode(" ", $errors)];
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
            $this->id = $this->conexion->insert_id;
            $stmt->close();
            
            // Notificar observers
            $userData = ['id' => $this->id, 'nombre' => $nombre_usuario, 'email' => $correo];
            $this->eventSubject->notifyUserRegistered($userData);
            
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
            
            // Notificar observers
            $userData = ['nombre' => $username];
            $this->eventSubject->notifyUserLogin($userData);
            
            return ["status" => "success", "message" => "Inicio de sesión exitoso"];
        } else {
            $stmt->close();
            return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
        }
    }
    
    public function cerrarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $username = $_SESSION['usuario'] ?? null;
        
        session_destroy();
        
        if ($username) {
            // Notificar observers
            $userData = ['nombre' => $username];
            $this->eventSubject->notifyUserLogout($userData);
        }
        
        return ["status" => "success", "message" => "Sesión cerrada exitosamente"];
    }
}

// Clase para usuarios administradores con Strategy específica
class UsuarioAdmin extends Usuario {
    private $privilegios;

    public function __construct($conexion, $datos = [], UserEventSubject $eventSubject = null) {
        // Usar Strategy de validación para administradores
        $adminValidator = new UserValidator(new AdminUserValidationStrategy());
        parent::__construct($conexion, $datos, $adminValidator, $eventSubject);
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
