<?php

interface IAuthService {
    public function login(string $username, string $password): array;
    public function logout(): array;
    public function register(string $nombre, string $email, string $password): array;
    public function isAuthenticated(): bool;
}

?>