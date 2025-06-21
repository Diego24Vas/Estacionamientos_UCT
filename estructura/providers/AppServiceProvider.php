<?php

require_once dirname(__DIR__) . '/core/ServiceProvider.php';
require_once dirname(__DIR__) . '/interfaces/IAuthService.php';
require_once dirname(__DIR__) . '/services/AuthService.php';
require_once dirname(__DIR__) . '/services/PaginationService.php';
require_once dirname(__DIR__) . '/services/ConfigService.php';
require_once dirname(__DIR__) . '/services/SessionManager.php';
require_once dirname(__DIR__) . '/services/VehicleService.php';
require_once dirname(__DIR__) . '/services/NotificationService.php';
require_once dirname(__DIR__) . '/services/ValidationService.php';
require_once dirname(__DIR__) . '/services/ReservaService.php';
require_once dirname(__DIR__) . '/services/ViewHelperService.php';

/**
 * Application Service Provider
 * Registra todos los servicios de aplicación
 */
class AppServiceProvider extends ServiceProvider {
      public function register(DIContainer $container): void {
        // Registrar ConfigService como singleton
        $this->singleton('service.config', function($container) {
            return new ConfigService();
        });
        
        // Registrar SessionManager como singleton
        $this->singleton('service.session', function($container) {
            return new SessionManager();
        });
        
        // Registrar ValidationService
        $this->bind('service.validation', function($container) {
            return new ValidationService();
        });
          // Registrar NotificationService
        $this->singleton('service.notification', function($container) {
            return new NotificationService($container->get('service.session'));
        });
        
        // Registrar ViewHelperService
        $this->singleton('service.view', function($container) {
            return new ViewHelperService(
                $container->get('service.config'),
                $container->get('service.session')
            );
        });
        
        // Registrar AuthService como singleton
        $this->singleton('service.auth', function($container) {
            return AuthService::getInstance($container->get('repository.user'));
        });
        
        // Alias para IAuthService
        $this->singleton(IAuthService::class, function($container) {
            return $container->get('service.auth');
        });
        
        // Registrar PaginationService
        $this->bind('service.pagination', function($container) {
            return new PaginationService();
        });
        
        // Registrar VehicleService
        $this->bind('service.vehicle', function($container) {
            return new VehicleService(
                $container->get('repository.vehicle'),
                $container->get('service.validation')
            );
        });
        
        // Registrar ReservaService
        $this->bind('service.reserva', function($container) {
            return new ReservaService(
                $container->get('repository.reserva'),
                $container->get('repository.vehicle'),
                $container->get('service.validation')
            );
        });
    }
}
