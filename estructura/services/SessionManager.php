<?php

/**
 * Session Manager
 * Maneja las sesiones de la aplicación usando DI
 */
class SessionManager {
    private bool $started = false;
    
    public function __construct() {
        $this->start();
    }
    
    /**
     * Iniciar sesión si no está iniciada
     */
    public function start(): void {
        if (!$this->started && session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
        }
    }
    
    /**
     * Obtener un valor de sesión
     */
    public function get(string $key, $default = null) {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Establecer un valor en la sesión
     */
    public function set(string $key, $value): void {
        $this->start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Verificar si existe una clave en la sesión
     */
    public function has(string $key): bool {
        $this->start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Eliminar una clave de la sesión
     */
    public function remove(string $key): void {
        $this->start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Limpiar toda la sesión
     */
    public function clear(): void {
        $this->start();
        session_unset();
    }
    
    /**
     * Destruir la sesión
     */
    public function destroy(): void {
        $this->start();
        session_destroy();
        $this->started = false;
    }
    
    /**
     * Regenerar ID de sesión
     */
    public function regenerate(bool $deleteOld = false): void {
        $this->start();
        session_regenerate_id($deleteOld);
    }
    
    /**
     * Obtener ID de sesión
     */
    public function getId(): string {
        $this->start();
        return session_id();
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated(): bool {
        return $this->has('user_id') && !empty($this->get('user_id'));
    }
    
    /**
     * Obtener usuario actual
     */
    public function getCurrentUser(): ?array {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return [
            'id' => $this->get('user_id'),
            'nombre' => $this->get('user_name'),
            'email' => $this->get('user_email'),
            'tipo' => $this->get('user_type')
        ];
    }
    
    /**
     * Establecer usuario en sesión
     */
    public function setUser(array $user): void {
        $this->set('user_id', $user['id'] ?? null);
        $this->set('user_name', $user['nombre'] ?? null);
        $this->set('user_email', $user['email'] ?? null);
        $this->set('user_type', $user['tipo'] ?? 'user');
    }
    
    /**
     * Cerrar sesión de usuario
     */
    public function logout(): void {
        $this->remove('user_id');
        $this->remove('user_name');
        $this->remove('user_email');
        $this->remove('user_type');
    }
    
    /**
     * Flashear un mensaje (solo para la próxima request)
     */
    public function flash(string $key, $value): void {
        $this->set("_flash.{$key}", $value);
    }
    
    /**
     * Obtener y eliminar mensaje flash
     */
    public function getFlash(string $key, $default = null) {
        $value = $this->get("_flash.{$key}", $default);
        $this->remove("_flash.{$key}");
        return $value;
    }
    
    /**
     * Verificar si hay mensaje flash
     */
    public function hasFlash(string $key): bool {
        return $this->has("_flash.{$key}");
    }
}
