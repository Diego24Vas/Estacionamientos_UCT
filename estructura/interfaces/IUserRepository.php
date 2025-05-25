<?php

interface IUserRepository {
    public function findByUsername(string $username): ?array;
    public function findByEmail(string $email): ?array;
    public function create(string $nombre, string $email, string $hashedPassword): bool;
    public function userExists(string $username, string $email): bool;
}

?>