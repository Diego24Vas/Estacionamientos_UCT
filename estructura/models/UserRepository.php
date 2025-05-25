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
}

?>