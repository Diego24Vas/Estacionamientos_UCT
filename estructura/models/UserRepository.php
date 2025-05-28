<?php

require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';

// Patrón Decorator para UserRepository
abstract class UserRepositoryDecorator implements IUserRepository {
    protected $userRepository;
    
    public function __construct(IUserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }
    
    public function findByUsername(string $username): ?array {
        return $this->userRepository->findByUsername($username);
    }
    
    public function findByEmail(string $email): ?array {
        return $this->userRepository->findByEmail($email);
    }
    
    public function create(string $nombre, string $email, string $hashedPassword): bool {
        return $this->userRepository->create($nombre, $email, $hashedPassword);
    }
    
    public function userExists(string $username, string $email): bool {
        return $this->userRepository->userExists($username, $email);
    }
    
    public function findAllWithPagination(int $offset, int $limit): array {
        return $this->userRepository->findAllWithPagination($offset, $limit);
    }
    
    public function getTotalUsersCount(): int {
        return $this->userRepository->getTotalUsersCount();
    }
    
    public function findUsersWithFilters(array $filters, int $offset, int $limit): array {
        return $this->userRepository->findUsersWithFilters($filters, $offset, $limit);
    }
    
    public function getTotalUsersCountWithFilters(array $filters): int {
        return $this->userRepository->getTotalUsersCountWithFilters($filters);
    }
}

// Decorator para logging
class LoggingUserRepositoryDecorator extends UserRepositoryDecorator {
    public function findByUsername(string $username): ?array {
        error_log("REPOSITORY: Buscando usuario por nombre: " . $username);
        $result = parent::findByUsername($username);
        error_log("REPOSITORY: Usuario encontrado: " . ($result ? 'Sí' : 'No'));
        return $result;
    }
    
    public function create(string $nombre, string $email, string $hashedPassword): bool {
        error_log("REPOSITORY: Creando usuario: " . $nombre . " (" . $email . ")");
        $result = parent::create($nombre, $email, $hashedPassword);
        error_log("REPOSITORY: Usuario creado: " . ($result ? 'Exitoso' : 'Fallido'));
        return $result;
    }
}

// Decorator para caché
class CachingUserRepositoryDecorator extends UserRepositoryDecorator {
    private $cache = [];
    private $cacheTimeout = 300; // 5 minutos
    
    public function findByUsername(string $username): ?array {
        $cacheKey = "user_by_username_" . md5($username);
        
        if (isset($this->cache[$cacheKey]) && 
            (time() - $this->cache[$cacheKey]['timestamp']) < $this->cacheTimeout) {
            error_log("CACHE: Usuario obtenido del caché: " . $username);
            return $this->cache[$cacheKey]['data'];
        }
        
        $result = parent::findByUsername($username);
        
        if ($result) {
            $this->cache[$cacheKey] = [
                'data' => $result,
                'timestamp' => time()
            ];
            error_log("CACHE: Usuario guardado en caché: " . $username);
        }
        
        return $result;
    }
    
    public function create(string $nombre, string $email, string $hashedPassword): bool {
        $result = parent::create($nombre, $email, $hashedPassword);
        
        // Limpiar caché relacionado cuando se crea un usuario
        if ($result) {
            $this->clearUserCache($nombre, $email);
        }
        
        return $result;
    }
    
    private function clearUserCache(string $username, string $email): void {
        $usernameKey = "user_by_username_" . md5($username);
        $emailKey = "user_by_email_" . md5($email);
        
        unset($this->cache[$usernameKey]);
        unset($this->cache[$emailKey]);
        
        error_log("CACHE: Caché limpiado para usuario: " . $username);
    }
}

class UserRepository implements IUserRepository {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->conexion->prepare("SELECT id, nombre, email, contraseña FROM INFO1170_RegistroUsuarios WHERE nombre = ?");
        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->conexion->prepare("SELECT id, nombre, email, contraseña FROM INFO1170_RegistroUsuarios WHERE email = ?");
        if ($stmt === false) {
            return null;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    public function create(string $nombre, string $email, string $hashedPassword): bool {
        $stmt = $this->conexion->prepare("INSERT INTO INFO1170_RegistroUsuarios (nombre, email, contraseña) VALUES (?, ?, ?)");
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("sss", $nombre, $email, $hashedPassword);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function userExists(string $username, string $email): bool {
        $stmt = $this->conexion->prepare("SELECT id FROM INFO1170_RegistroUsuarios WHERE nombre = ? OR email = ?");
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    /**
     * Obtener usuarios con paginación
     */
    public function findAllWithPagination(int $offset, int $limit): array {
        $stmt = $this->conexion->prepare("SELECT id, nombre, email, fecha_registro FROM INFO1170_RegistroUsuarios ORDER BY id DESC LIMIT ? OFFSET ?");
        if ($stmt === false) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        $stmt->close();
        return $users;
    }

    /**
     * Obtener el total de usuarios
     */
    public function getTotalUsersCount(): int {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM INFO1170_RegistroUsuarios");
        if ($stmt === false) {
            return 0;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int) $row['total'];
    }

    /**
     * Buscar usuarios con filtros y paginación
     */
    public function findUsersWithFilters(array $filters, int $offset, int $limit): array {
        $whereClause = "WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filters['nombre'])) {
            $whereClause .= " AND nombre LIKE ?";
            $params[] = '%' . $filters['nombre'] . '%';
            $types .= "s";
        }

        if (!empty($filters['email'])) {
            $whereClause .= " AND email LIKE ?";
            $params[] = '%' . $filters['email'] . '%';
            $types .= "s";
        }

        $sql = "SELECT id, nombre, email, fecha_registro FROM INFO1170_RegistroUsuarios $whereClause ORDER BY id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        $stmt->close();
        return $users;
    }

    /**
     * Obtener total de usuarios con filtros
     */
    public function getTotalUsersCountWithFilters(array $filters): int {
        $whereClause = "WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filters['nombre'])) {
            $whereClause .= " AND nombre LIKE ?";
            $params[] = '%' . $filters['nombre'] . '%';
            $types .= "s";
        }

        if (!empty($filters['email'])) {
            $whereClause .= " AND email LIKE ?";
            $params[] = '%' . $filters['email'] . '%';
            $types .= "s";
        }

        $sql = "SELECT COUNT(*) as total FROM INFO1170_RegistroUsuarios $whereClause";
        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            return 0;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int) $row['total'];
    }
}

?>