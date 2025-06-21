<?php

require_once dirname(__DIR__) . '/core/ServiceProvider.php';
require_once dirname(__DIR__) . '/interfaces/IUserRepository.php';
require_once dirname(__DIR__) . '/models/UserRepository.php';

/**
 * Repository Service Provider
 * Registra todos los repositories con sus decoradores
 */
class RepositoryServiceProvider extends ServiceProvider {
    
    public function register(DIContainer $container): void {
        // Registrar UserRepository base
        $this->bind('repository.user.base', function($container) {
            return new UserRepository($container->get('conexion'));
        });
        
        // Registrar UserRepository con decorador de logging
        $this->bind('repository.user.logged', function($container) {
            $baseRepository = $container->get('repository.user.base');
            return new LoggingUserRepositoryDecorator($baseRepository);
        });
        
        // Registrar UserRepository completo con todos los decoradores
        $this->singleton('repository.user', function($container) {
            $loggedRepository = $container->get('repository.user.logged');
            return new CachingUserRepositoryDecorator($loggedRepository);
        });
        
        // Alias para IUserRepository
        $this->singleton(IUserRepository::class, function($container) {
            return $container->get('repository.user');
        });
        
        // Registrar VehicleRepository (cuando se implemente)
        $this->bind('repository.vehicle', function($container) {
            // TODO: Implementar VehicleRepository
            return new VehicleRepository($container->get('database'));
        });
        
        // Registrar ReservaRepository (cuando se implemente)
        $this->bind('repository.reserva', function($container) {
            // TODO: Implementar ReservaRepository
            return new ReservaRepository($container->get('database'));
        });
    }
}
