<?php

require_once dirname(__DIR__) . '/core/ServiceProvider.php';
require_once dirname(__DIR__) . '/config/conex.php';

/**
 * Database Service Provider
 * Registra servicios relacionados con la base de datos
 */
class DatabaseServiceProvider extends ServiceProvider {
    
    public function register(DIContainer $container): void {
        // Registrar conexión PDO como singleton
        $this->singleton('database.pdo', function($container) {
            global $host, $user, $password, $BD;
            
            try {
                $pdo = new PDO("mysql:host={$host};dbname={$BD};charset=utf8mb4", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                return $pdo;
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        });
        
        // Registrar conexión mysqli como singleton (para compatibilidad)
        $this->singleton('database.mysqli', function($container) {
            global $host, $user, $password, $BD;
            
            $connection = new mysqli($host, $user, $password, $BD);
            
            if ($connection->connect_error) {
                throw new Exception("Database connection failed: " . $connection->connect_error);
            }
            
            $connection->set_charset("utf8mb4");
            return $connection;
        });
        
        // Alias para compatibilidad
        $this->singleton('database', function($container) {
            return $container->get('database.pdo');
        });
        
        // Registrar connection legacy global
        $this->singleton('conexion', function($container) {
            return $container->get('database.mysqli');
        });
    }
}
