<?php

require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';

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