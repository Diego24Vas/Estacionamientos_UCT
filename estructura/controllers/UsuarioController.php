<?php

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/conex.php';
require_once dirname(__DIR__) . '/models/UserRepository.php';
require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/services/PaginationService.php';
require_once dirname(__DIR__) . '/interfaces/IAuthService.php';
require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';
require_once dirname(__DIR__) . '/interfaces/IPaginationService.php';

class UsuarioController {
    private $authService;
    private $userRepository;
    private $paginationService;

    public function __construct() {
        global $conexion;
        
        // Inyección de dependencias siguiendo el principio DIP
        $this->userRepository = new UserRepository($conexion);
        $this->authService = new AuthService($this->userRepository);
        $this->paginationService = new PaginationService();
    }

    /**
     * Método para manejar el inicio de sesión
     * @return void
     */
    public function login(): void {
        // Verificar que sea método POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->sendJsonResponse(["status" => "error", "message" => "Método no permitido"]);
            return;
        }

        // Obtener datos del formulario
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        try {
            // Delegar la lógica al servicio de autenticación
            $resultado = $this->authService->login($username, $password);
            $this->sendJsonResponse($resultado);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error interno del servidor"
            ]);
        }
    }

    /**
     * Método para manejar el cierre de sesión
     * @return void
     */
    public function logout(): void {
        try {
            $resultado = $this->authService->logout();
            $this->sendJsonResponse($resultado);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error al cerrar sesión"
            ]);
        }
    }

    /**
     * Método para manejar el registro de usuarios
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
            // Delegar la lógica al servicio de autenticación
            $resultado = $this->authService->register($nombre, $email, $password);
            $this->sendJsonResponse($resultado);
        } catch (Exception $e) {
            $this->sendJsonResponse([
                "status" => "error", 
                "message" => "Error interno del servidor"
            ]);
        }
    }

    /**
     * Método para verificar si el usuario está autenticado
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
        echo json_encode($data);
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
            
            // Sanitizar parámetros
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
            case 'usuario-actual':
                $this->obtenerUsuarioActual();
                break;
            case 'listar-usuarios':
                $this->listarUsuarios();
                break;
            case 'buscar-usuarios':
                $this->buscarUsuarios();
                break;
            default:
                $this->sendJsonResponse([
                    "status" => "error", 
                    "message" => "Acción no válida"
                ]);
        }
    }
}

?>