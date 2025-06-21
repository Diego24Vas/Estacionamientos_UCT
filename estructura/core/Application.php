<?php

require_once __DIR__ . '/DIContainer.php';
require_once __DIR__ . '/ServiceProvider.php';
require_once dirname(__DIR__) . '/providers/DatabaseServiceProvider.php';
require_once dirname(__DIR__) . '/providers/RepositoryServiceProvider.php';
require_once dirname(__DIR__) . '/providers/AppServiceProvider.php';


class Application {
    private static $instance = null;
    private $container;
    private $providers = [];
    
    private function __construct() {
        $this->container = new DIContainer();
        $this->registerCoreServices();
        $this->registerServiceProviders();
    }
    
    /**
     * Singleton pattern para Application
     */
    public static function getInstance(): Application {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtener el container de DI
     */
    public function getContainer(): DIContainer {
        return $this->container;
    }
    
    /**
     * Resolver un servicio del container
     */
    public function get(string $name) {
        return $this->container->get($name);
    }
    
    /**
     * Crear una instancia usando auto-wiring
     */
    public function make(string $className) {
        return $this->container->make($className);
    }
      /**
     * Registrar servicios core
     */
    private function registerCoreServices(): void {
        // Registrar el container mismo
        $this->container->instance('container', $this->container);
        $this->container->instance('app', $this);
    }
    
    /**
     * Registrar todos los service providers
     */
    private function registerServiceProviders(): void {
        $this->providers = [
            new DatabaseServiceProvider($this->container),
            new RepositoryServiceProvider($this->container),
            new AppServiceProvider($this->container)
        ];
        
        foreach ($this->providers as $provider) {
            $provider->register($this->container);
        }
    }
    
    /**
     * Método helper para obtener servicios de forma estática
     */
    public static function resolve(string $name) {
        return self::getInstance()->get($name);
    }
    
    /**
     * Método helper para crear instancias de forma estática
     */
    public static function makeInstance(string $className) {
        return self::getInstance()->make($className);
    }
}

/**
 * Helper functions globales
 */
function app(string $service = null) {
    $app = Application::getInstance();
    
    if ($service === null) {
        return $app;
    }
    
    return $app->get($service);
}

function container(): DIContainer {
    return Application::getInstance()->getContainer();
}

function resolve(string $service) {
    return Application::resolve($service);
}

function makeInstance(string $className) {
    return Application::makeInstance($className);
}
