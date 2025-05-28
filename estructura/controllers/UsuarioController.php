<?php

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/conex.php';
require_once dirname(__DIR__) . '/models/UserRepository.php';
require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/services/PaginationService.php';
require_once dirname(__DIR__) . '/interfaces/IAuthService.php';
require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';
require_once dirname(__DIR__) . '/interfaces/IPaginationService.php';

// Patrón Command para acciones de usuario
interface UserCommand {
    public function execute(): array;
}

class LoginCommand implements UserCommand {
    private $authService;
    private $username;
    private $password;

    public function __construct(AuthService $authService, string $username, string $password) {
        $this->authService = $authService;
        $this->username = $username;
        $this->password = $password;
    }

    public function execute(): array {
        return $this->authService->login($this->username, $this->password);
    }
}

class RegisterCommand implements UserCommand {
    private $authService;
    private $nombre;
    private $email;
    private $password;

    public function __construct(AuthService $authService, string $nombre, string $email, string $password) {
        $this->authService = $authService;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
    }

    public function execute(): array {
        return $this->authService->register($this->nombre, $this->email, $this->password);
    }
}

class LogoutCommand implements UserCommand {
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function execute(): array {
        return $this->authService->logout();
    }
}

// Invoker para el patrón Command
class UserCommandInvoker {
    private $command;

    public function setCommand(UserCommand $command): void {
        $this->command = $command;
    }

    public function executeCommand(): array {
        if ($this->command === null) {
            return ["status" => "error", "message" => "No hay comando para ejecutar"];
        }
        return $this->command->execute();
    }
}

class UsuarioController {
    private $authService;
    private $userRepository;
    private $paginationService;
    private $commandInvoker;

    public function __construct() {
        global $conexion;
        
        // Usar Singleton para AuthService
        $baseRepository = new UserRepository($conexion);
        $loggedRepository = new LoggingUserRepositoryDecorator($baseRepository);
        $decoratedRepository = new CachingUserRepositoryDecorator($loggedRepository);
        
        $this->userRepository = $decoratedRepository;
        $this->authService = AuthService::getInstance($this->userRepository);
        $this->paginationService = new PaginationService();
        $this->commandInvoker = new UserCommandInvoker();
    }

    /**
     * Método para manejar el inicio de sesión usando Command pattern
     * @return void
     */
    public function login(): void {
        // Verificar que sea método POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->sendJsonResponse(["status" => "error", "message" => "Método no permitido"]);
            return;
        }

        // Limpiar sesiones problemáticas antes del login
        $this->authService->cleanSession();

        // Obtener datos del formulario
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        try {
            // Usar Command pattern
            $loginCommand = new LoginCommand($this->authService, $username, $password);
            $this->commandInvoker->setCommand($loginCommand);
            $resultado = $this->commandInvoker->executeCommand();
            
            $this->sendJsonResponse($resultado);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error interno del servidor"
            ]);
        }
    }

    /**
     * Método para manejar el cierre de sesión usando Command pattern
     * @return void
     */
    public function logout(): void {
        try {
            $logoutCommand = new LogoutCommand($this->authService);
            $this->commandInvoker->setCommand($logoutCommand);
            $resultado = $this->commandInvoker->executeCommand();
            
            $this->sendJsonResponse($resultado);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al cerrar sesión"
            ]);
        }
    }

    /**
     * Método para manejar el registro usando Command pattern
     * @return void
     */
    public function registro(): void {
        // Verificar que sea método POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->sendJsonResponse(["status" => "error", "message" => "Método no permitido"]);
            return;
        }

        // Obtener datos del formulario
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        try {
            // Usar Command pattern
            $registerCommand = new RegisterCommand($this->authService, $nombre, $email, $password);
            $this->commandInvoker->setCommand($registerCommand);
            $resultado = $this->commandInvoker->executeCommand();
            
            $this->sendJsonResponse($resultado);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al registrar usuario"
            ]);
        }
    }

    /**
     * Método para verificar autenticación
     * @return void
     */
    public function verificarAutenticacion(): void {
        try {
            $isAuthenticated = $this->authService->isAuthenticated();
            $this->sendJsonResponse([
                "status" => "success", 
                "authenticated" => $isAuthenticated
            ]);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al verificar autenticación"
            ]);
        }
    }

    /**
     * Método para obtener información del usuario actual
     * @return void
     */
    public function obtenerUsuarioActual(): void {
        if (!$this->authService->isAuthenticated()) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "No autenticado"
            ]);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->sendJsonResponse([
            "status" => "success",
            "user" => [
                "id" => $_SESSION['user_id'] ?? null,
                "username" => $_SESSION['usuario'] ?? null,
                "email" => $_SESSION['user_email'] ?? null
            ]
        ]);
    }

    /**
     * Método privado para enviar respuestas JSON
     * @param array $data
     * @return void
     */
    private function sendJsonResponse(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Método para listar usuarios con paginación
     * @return void
     */
    public function listarUsuarios(): void {
        // Verificar autenticación
        if (!$this->authService->isAuthenticated()) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "No autenticado"
            ]);
            return;
        }

        try {
            // Obtener parámetros de paginación
            $page = (int) ($_GET['page'] ?? 1);
            $limit = (int) ($_GET['limit'] ?? 10);

            // Sanitizar y validar parámetros
            $params = $this->paginationService->sanitizePaginationParams($page, $limit);
            $page = $params['page'];
            $limit = $params['limit'];

            $validation = $this->paginationService->validatePaginationParams($page, $limit);
            if ($validation['status'] === 'error') {
                $this->sendJsonResponse($validation);
                return;
            }

            // Calcular offset
            $offset = $this->paginationService->calculateOffset($page, $limit);

            // Obtener usuarios y total
            $users = $this->userRepository->findAllWithPagination($offset, $limit);
            $totalUsers = $this->userRepository->getTotalUsersCount();

            // Generar información de paginación
            $paginationInfo = $this->paginationService->getPaginationInfo($totalUsers, $page, $limit);

            $this->sendJsonResponse([
                "status" => "success",
                "data" => $users,
                "pagination" => $paginationInfo
            ]);

        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al obtener usuarios"
            ]);
        }
    }

    /**
     * Método para buscar usuarios con filtros y paginación
     * @return void
     */
    public function buscarUsuarios(): void {
        // Verificar autenticación
        if (!$this->authService->isAuthenticated()) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "No autenticado"
            ]);
            return;
        }

        try {
            // Obtener parámetros de búsqueda y paginación
            $page = (int) ($_GET['page'] ?? 1);
            $limit = (int) ($_GET['limit'] ?? 10);
            $nombre = trim($_GET['nombre'] ?? '');
            $email = trim($_GET['email'] ?? '');

            // Preparar filtros
            $filters = [];
            if (!empty($nombre)) {
                $filters['nombre'] = $nombre;
            }
            if (!empty($email)) {
                $filters['email'] = $email;
            }

            // Sanitizar parámetros de paginación
            $params = $this->paginationService->sanitizePaginationParams($page, $limit);
            $page = $params['page'];
            $limit = $params['limit'];

            // Validar parámetros
            $validation = $this->paginationService->validatePaginationParams($page, $limit);
            if ($validation['status'] === 'error') {
                $this->sendJsonResponse($validation);
                return;
            }

            // Calcular offset
            $offset = $this->paginationService->calculateOffset($page, $limit);

            // Buscar usuarios con filtros
            $users = $this->userRepository->findUsersWithFilters($filters, $offset, $limit);
            $totalUsers = $this->userRepository->getTotalUsersCountWithFilters($filters);

            // Generar información de paginación
            $paginationInfo = $this->paginationService->getPaginationInfo($totalUsers, $page, $limit);

            $this->sendJsonResponse([
                "status" => "success",
                "data" => $users,
                "pagination" => $paginationInfo,
                "filters" => $filters
            ]);

        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al buscar usuarios"
            ]);
        }
    }

    /**
     * Método para limpiar sesiones problemáticas manualmente
     * @return void
     */
    public function limpiarSesion(): void {
        try {
            $this->authService->cleanSession();
            $this->sendJsonResponse([
                "status" => "success", 
                "message" => "Sesión limpiada exitosamente"
            ]);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al limpiar sesión"
            ]);
        }
    }

    /**
     * Método para manejar rutas dinámicamente
     * @param string $action
     * @return void
     */
    public function handleRequest(string $action): void {
        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'registro':
                $this->registro();
                break;
            case 'verificar-auth':
                $this->verificarAutenticacion();
                break;
            case 'limpiar-sesion':
                $this->limpiarSesion();
                break;
            case 'listar':
                $this->listarUsuarios();
                break;
            case 'buscar':
                $this->buscarUsuarios();
                break;
            default:
                $this->sendJsonResponse([
                    "status" => "error", 
                    "message" => "Acción no válida"
                ]);
                break;
        }
    }
}

?>