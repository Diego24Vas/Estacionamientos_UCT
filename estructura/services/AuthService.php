<?php

require_once dirname(__DIR__) . '/interfaces/IAuthService.php';
require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';

class AuthService implements IAuthService {
    private $userRepository;

    public function __construct(IUserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): array {
        // Validación básica
        if (empty($username) || empty($password)) {
            return ["status" => "error", "message" => "Por favor, completa todos los campos."];
        }

        // Buscar usuario
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user) {
            return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
        }

        // Verificar contraseña
        if (!password_verify($password, $user['contraseña'])) {
            return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
        }

        // Iniciar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['usuario'] = $username;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        return ["status" => "success", "message" => "Inicio de sesión exitoso"];
    }

    public function logout(): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpiar todas las variables de sesión
        $_SESSION = array();

        // Destruir la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        return ["status" => "success", "message" => "Sesión cerrada exitosamente"];
    }

    public function register(string $nombre, string $email, string $password): array {
        // Validación de datos
        if (empty($nombre) || empty($email) || empty($password)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo proporcionado no es válido."];
        }

        if (strlen($password) < 6) {
            return ["status" => "error", "message" => "La contraseña debe tener al menos 6 caracteres."];
        }

        // Verificar si el usuario ya existe
        if ($this->userRepository->userExists($nombre, $email)) {
            return ["status" => "error", "message" => "El usuario o correo ya está registrado."];
        }

        // Hashear contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Crear usuario
        if ($this->userRepository->create($nombre, $email, $hashedPassword)) {
            return ["status" => "success", "message" => "Registro exitoso"];
        } else {
            return ["status" => "error", "message" => "Error al registrar usuario."];
        }
    }

    public function isAuthenticated(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }
}

?>