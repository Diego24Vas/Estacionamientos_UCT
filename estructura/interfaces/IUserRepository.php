<?php

interface IUserRepository {
    public function findByUsername(string $username): ?array;
    public function findByEmail(string $email): ?array;
    public function create(string $nombre, string $email, string $hashedPassword): bool;
    public function userExists(string $username, string $email): bool;
    
    // Métodos para paginación
    public function findAllWithPagination(int $offset, int $limit): array;
    public function getTotalUsersCount(): int;
    public function findUsersWithFilters(array $filters, int $offset, int $limit): array;
    public function getTotalUsersCountWithFilters(array $filters): int;
}

?>