<?php

require_once dirname(__DIR__) . '/interfaces/IAuthService.php';
require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';
require_once dirname(__DIR__) . '/models/UserRepository.php';

// Patrón Singleton + Template Method para AuthService
class AuthService implements IAuthService {
    private static $instance = null;
    private $userRepository;
    private $sessionManager;

    // Constructor privado para Singleton
    private function __construct(IUserRepository $userRepository = null) {
        global $conexion;
        
        if ($userRepository === null) {
            // Usar decorators por defecto
            $baseRepository = new UserRepository($conexion);
            $loggedRepository = new LoggingUserRepositoryDecorator($baseRepository);
            $this->userRepository = new CachingUserRepositoryDecorator($loggedRepository);
        } else {
            $this->userRepository = $userRepository;
        }
        
        $this->sessionManager = new AuthSessionManager();
    }

    // Método estático para obtener la instancia (Singleton)
    public static function getInstance(IUserRepository $userRepository = null): AuthService {
        if (self::$instance === null) {
            self::$instance = new self($userRepository);
        }
        return self::$instance;
    }

    // Prevenir clonación
    private function __clone() {}

    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    // Template Method para autenticación
    public function login(string $username, string $password): array {
        // Paso 1: Validar entrada
        $validationResult = $this->validateLoginInput($username, $password);
        if ($validationResult !== true) {
            return $validationResult;
        }

        // Paso 2: Buscar usuario
        $user = $this->findUser($username);
        if (!$user) {
            return $this->handleUserNotFound();
        }

        // Paso 3: Verificar credenciales
        if (!$this->verifyCredentials($password, $user['contraseña'])) {
            return $this->handleInvalidCredentials();
        }

        // Paso 4: Iniciar sesión
        return $this->startSession($user);
    }

    // Template Method para registro
    public function register(string $nombre, string $email, string $password): array {
        // Paso 1: Validar entrada
        $validationResult = $this->validateRegisterInput($nombre, $email, $password);
        if ($validationResult !== true) {
            return $validationResult;
        }

        // Paso 2: Verificar disponibilidad
        if ($this->userRepository->userExists($nombre, $email)) {
            return $this->handleUserExists();
        }

        // Paso 3: Procesar contraseña
        $hashedPassword = $this->processPassword($password);

        // Paso 4: Crear usuario
        if ($this->userRepository->create($nombre, $email, $hashedPassword)) {
            return $this->handleRegistrationSuccess();
        } else {
            return $this->handleRegistrationError();
        }
    }

    public function logout(): array {
        return $this->sessionManager->destroySession();
    }

    public function isAuthenticated(): bool {
        return $this->sessionManager->isSessionActive();
    }

    /**
     * Método público para limpiar sesiones problemáticas
     * @return void
     */
    public function cleanSession(): void {
        $this->sessionManager->cleanSession();
    }

    // Métodos del Template Method (pueden ser sobrescritos)
    protected function validateLoginInput(string $username, string $password) {
        if (empty($username) || empty($password)) {
            return ["status" => "error", "message" => "Por favor, completa todos los campos."];
        }
        return true;
    }

    protected function validateRegisterInput(string $nombre, string $email, string $password) {
        if (empty($nombre) || empty($email) || empty($password)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo proporcionado no es válido."];
        }
        if (strlen($password) < 6) {
            return ["status" => "error", "message" => "La contraseña debe tener al menos 6 caracteres."];
        }
        return true;
    }

    protected function findUser(string $username): ?array {
        return $this->userRepository->findByUsername($username);
    }

    protected function verifyCredentials(string $password, string $hashedPassword): bool {
        return password_verify($password, $hashedPassword);
    }

    protected function processPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function startSession(array $user): array {
        $sessionResult = $this->sessionManager->startSession($user);
        if ($sessionResult['status'] === 'success') {
            return ["status" => "success", "message" => "Inicio de sesión exitoso", "user" => $user];
        }
        return $sessionResult;
    }

    protected function handleUserNotFound(): array {
        return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
    }

    protected function handleInvalidCredentials(): array {
        return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
    }

    protected function handleUserExists(): array {
        return ["status" => "error", "message" => "El usuario o correo ya está registrado."];
    }

    protected function handleRegistrationSuccess(): array {
        return ["status" => "success", "message" => "Registro exitoso"];
    }

    protected function handleRegistrationError(): array {
        return ["status" => "error", "message" => "Error al registrar usuario."];
    }
}

// Patrón State para gestión de sesiones
interface SessionState {
    public function startSession(array $userData): array;
    public function destroySession(): array;
    public function isActive(): bool;
}

class ActiveSessionState implements SessionState {
    public function startSession(array $userData): array {
        // Permitir re-login: cerrar sesión actual y crear nueva
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar datos de sesión anterior
        session_unset();
        
        // Establecer nuevos datos de sesión
        $_SESSION['usuario'] = $userData['nombre'];
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['login_time'] = time();
        
        return ["status" => "success", "message" => "Sesión actualizada exitosamente"];
    }

    public function destroySession(): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar todas las variables de sesión
        session_unset();
        
        // Destruir la sesión
        session_destroy();
        
        return ["status" => "success", "message" => "Sesión cerrada exitosamente"];
    }

    public function isActive(): bool {
        return true;
    }
}

class InactiveSessionState implements SessionState {
    public function startSession(array $userData): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['usuario'] = $userData['nombre'];
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['login_time'] = time();
        
        return ["status" => "success", "message" => "Sesión iniciada exitosamente"];
    }

    public function destroySession(): array {
        return ["status" => "error", "message" => "No hay sesión activa"];
    }

    public function isActive(): bool {
        return false;
    }
}

// Context para el patrón State
class AuthSessionManager {
    private $state;

    public function __construct() {
        $this->state = $this->getCurrentState();
    }

    private function getCurrentState(): SessionState {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si hay una sesión válida
        if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
            return new ActiveSessionState();
        } else {
            return new InactiveSessionState();
        }
    }

    public function startSession(array $userData): array {
        // Actualizar estado antes de intentar iniciar sesión
        $this->state = $this->getCurrentState();
        
        $result = $this->state->startSession($userData);
        
        // Actualizar estado después de la operación
        $this->state = $this->getCurrentState();
        
        return $result;
    }

    public function destroySession(): array {
        // Actualizar estado antes de intentar cerrar sesión
        $this->state = $this->getCurrentState();
        
        $result = $this->state->destroySession();
        
        // Actualizar estado después de la operación
        $this->state = $this->getCurrentState();
        
        return $result;
    }

    public function isSessionActive(): bool {
        // Siempre actualizar el estado antes de verificar
        $this->state = $this->getCurrentState();
        return $this->state->isActive();
    }

    /**
     * Método para limpiar sesiones expiradas o corruptas
     */
    public function cleanSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si la sesión tiene datos incompletos o corruptos
        if (isset($_SESSION['usuario']) && (empty($_SESSION['usuario']) || !isset($_SESSION['user_id']))) {
            session_unset();
            session_destroy();
        }
    }
}
?>