<?php

require_once dirname(__DIR__) . '/interfaces/IAuthService.php';
require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';

/**
 * Clase Autenticador que implementa la interfaz IAuthService
 * 
 * Esta clase se encarga de la autenticación de usuarios, incluyendo:
 * - Iniciar sesión
 * - Cerrar sesión
 * - Validación de credenciales
 * - Protección contra ataques CSRF
 * 
 * Utiliza el patrón Singleton para asegurar una única instancia
 * y el patrón Template Method para estructurar el flujo de autenticación
 */
class Autenticador implements IAuthService {
    private static $instance = null;
    private $userRepository;
    private $auth;

    /**
     * Constructor privado (Singleton)
     */
    private function __construct(IUserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->auth = new Auth();
    }

    /**
     * Método estático para obtener la instancia (Singleton)
     */
    public static function getInstance(IUserRepository $userRepository): Autenticador {
        if (self::$instance === null) {
            self::$instance = new self($userRepository);
        }
        return self::$instance;
    }    /**
     * Método para iniciar sesión
     * Implementa un flujo de Template Method
     */
    public function login(string $username, string $password): array {
        // 1. Validar entrada
        if (!$this->validarDatosEntrada($username, $password)) {
            return ["status" => "error", "message" => "Por favor, completa todos los campos correctamente."];
        }
        
        // 2. Verificar token CSRF si está presente
        if (isset($_POST['csrf_token'])) {
            if (!$this->auth->verificarCSRFToken($_POST['csrf_token'])) {
                return ["status" => "error", "message" => "Error de seguridad: token CSRF inválido."];
            }
        }

        // 3. Buscar usuario
        $usuario = $this->userRepository->findByUsername($username);
        if (!$usuario) {
            return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
        }

        // 4. Verificar contraseña
        if (!$this->verificarCredenciales($password, $usuario['contraseña'])) {
            return ["status" => "error", "message" => "Usuario o contraseña incorrectos."];
        }

        // 5. Iniciar sesión
        return $this->auth->iniciarSesion($usuario);
    }

    /**
     * Método para cerrar sesión
     */
    public function logout(): array {
        // Verificar token CSRF si está presente
        if (isset($_POST['csrf_token']) || isset($_GET['csrf_token'])) {
            $token = $_POST['csrf_token'] ?? $_GET['csrf_token'];
            if (!$this->auth->verificarCSRFToken($token)) {
                return ["status" => "error", "message" => "Error de seguridad: token CSRF inválido."];
            }
        }
        
        return $this->auth->cerrarSesion();
    }    /**
     * Método para registrar nuevo usuario
     */
    public function register(string $nombre, string $email, string $password): array {
        // 1. Validar datos
        if (empty($nombre) || empty($email) || empty($password)) {
            return ["status" => "error", "message" => "Todos los campos son obligatorios."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "El correo proporcionado no es válido."];
        }

        if (strlen($password) < 6) {
            return ["status" => "error", "message" => "La contraseña debe tener al menos 6 caracteres."];
        }
        
        // 2. Verificar token CSRF si está presente
        if (isset($_POST['csrf_token'])) {
            if (!$this->auth->verificarCSRFToken($_POST['csrf_token'])) {
                return ["status" => "error", "message" => "Error de seguridad: token CSRF inválido."];
            }
        }

        // 3. Verificar que no exista el usuario
        if ($this->userRepository->userExists($nombre, $email)) {
            return ["status" => "error", "message" => "El usuario o correo ya está registrado."];
        }

        // 4. Hashear contraseña
        $hashedPassword = $this->hashearPassword($password);

        // 5. Crear usuario
        if ($this->userRepository->create($nombre, $email, $hashedPassword)) {
            return ["status" => "success", "message" => "Usuario registrado correctamente"];
        } else {
            return ["status" => "error", "message" => "Error al registrar el usuario"];
        }
    }/**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated(): bool {
        return $this->auth->sesionActiva();
    }
    
    /**
     * Genera un token CSRF para proteger formularios
     */
    public function generarCSRFToken(): string {
        return $this->auth->generarCSRFToken();
    }
    
    /**
     * Obtiene el HTML para el campo CSRF oculto
     */
    public function obtenerCSRFTokenHTML(): string {
        $token = $this->auth->generarCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }    /**
     * Método privado para validar datos de entrada
     */
    private function validarDatosEntrada(string $username, string $password): bool {
        return !empty($username) && !empty($password);
    }

    /**
     * Método privado para verificar credenciales
     */
    private function verificarCredenciales(string $password, string $hashedPassword): bool {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Método privado para hashear contraseña
     */
    private function hashearPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Evitar clonación (parte del patrón Singleton)
     */
    private function __clone() {}

    /**
     * Evitar deserialización (parte del patrón Singleton)
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar una instancia de Autenticador");
    }
}

/**
 * Clase Auth para gestión de sesiones y seguridad
 */
class Auth {
    private $tokenPrefix = 'csrf_';
    private $tokenExpiry = 7200; // 2 horas en segundos
    
    /**
     * Constructor: inicia la sesión si no está activa
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar cookies seguras
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params(
                $cookieParams["lifetime"],
                $cookieParams["path"],
                $cookieParams["domain"],
                true,    // Secure flag (solo HTTPS)
                true     // HttpOnly flag
            );
            
            session_start();
        }
        
        // Regenerar ID de sesión periódicamente (cada 30 minutos)
        if (!isset($_SESSION['last_regeneration']) || 
            (time() - $_SESSION['last_regeneration']) > 1800) {
            $this->regenerarSesion();
        }
    }
    
    /**
     * Regenera la sesión para prevenir ataques de fijación de sesión
     */
    private function regenerarSesion() {
        $oldSession = $_SESSION;
        session_regenerate_id(true);
        $_SESSION = $oldSession;
        $_SESSION['last_regeneration'] = time();
    }
    
    /**
     * Iniciar sesión
     */
    public function iniciarSesion(array $usuario): array {
        // Limpiar sesión anterior
        $this->limpiarSesion();
        
        // Regenerar ID de sesión para prevenir ataques de fijación de sesión
        session_regenerate_id(true);
        
        // Establecer datos de sesión
        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['last_activity'] = time();
        $_SESSION['last_regeneration'] = time();
        
        // Registrar evento de login exitoso
        error_log("Login exitoso: " . $usuario['nombre']);
        
        return [
            "status" => "success", 
            "message" => "Inicio de sesión exitoso",
            "user" => [
                "id" => $usuario['id'],
                "nombre" => $usuario['nombre'],
                "email" => $usuario['email']
            ]
        ];
    }
    
    /**
     * Cerrar sesión
     */
    public function cerrarSesion(): array {
        // Guardar nombre de usuario para registro
        $nombre_usuario = $_SESSION['usuario'] ?? 'Usuario desconocido';
        
        // Destruir la sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Registrar evento de logout
        error_log("Logout: " . $nombre_usuario);
        
        return ["status" => "success", "message" => "Sesión cerrada correctamente"];
    }
    
    /**
     * Verificar si hay una sesión activa y válida
     */
    public function sesionActiva(): bool {
        if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
            return false;
        }
        
        // Verificar tiempo de inactividad (30 minutos)
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > 1800)) {
            $this->cerrarSesion();
            return false;
        }
        
        // Verificar si el IP y User-Agent coinciden
        if (isset($_SESSION['ip']) && isset($_SESSION['user_agent'])) {
            if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
                $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                $this->cerrarSesion();
                return false;
            }
        }
        
        // Actualizar tiempo de última actividad
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Limpiar datos de sesión
     */
    public function limpiarSesion(): void {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, $this->tokenPrefix) !== 0) { // Preservar tokens CSRF
                unset($_SESSION[$key]);
            }
        }
    }
    
    /**
     * Genera un token CSRF único y lo almacena en la sesión
     */
    public function generarCSRFToken(): string {
        $token = bin2hex(random_bytes(32));
        $tokenName = $this->tokenPrefix . $token;
        
        $_SESSION[$tokenName] = [
            'token' => $token,
            'expires' => time() + $this->tokenExpiry
        ];
        
        // Limpiar tokens vencidos
        $this->limpiarTokensVencidos();
        
        return $token;
    }
    
    /**
     * Verifica si un token CSRF es válido
     */
    public function verificarCSRFToken(string $token): bool {
        $tokenName = $this->tokenPrefix . $token;
        
        if (!isset($_SESSION[$tokenName])) {
            return false;
        }
        
        $tokenData = $_SESSION[$tokenName];
        
        // Verificar expiración
        if (time() > $tokenData['expires']) {
            unset($_SESSION[$tokenName]);
            return false;
        }
        
        // Eliminar token usado (one-time use)
        unset($_SESSION[$tokenName]);
        
        return true;
    }
    
    /**
     * Limpia tokens CSRF vencidos de la sesión
     */
    private function limpiarTokensVencidos(): void {
        foreach ($_SESSION as $key => $value) {
            // Solo procesar tokens CSRF
            if (strpos($key, $this->tokenPrefix) === 0) {
                if (isset($value['expires']) && time() > $value['expires']) {
                    unset($_SESSION[$key]);
                }
            }
        }
    }
}
?>
