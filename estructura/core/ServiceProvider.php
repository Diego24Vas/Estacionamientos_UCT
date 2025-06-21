<?php

/**
 * Interface para Service Providers
 */
interface ServiceProviderInterface {
    public function register(DIContainer $container): void;
}

/**
 * Service Provider base abstracto
 */
abstract class ServiceProvider implements ServiceProviderInterface {
    protected $container;
    
    public function __construct(DIContainer $container) {
        $this->container = $container;
    }
    
    abstract public function register(DIContainer $container): void;
    
    /**
     * Método helper para registrar servicios
     */
    protected function bind(string $abstract, callable $concrete, bool $singleton = false): void {
        $this->container->register($abstract, $concrete, $singleton);
    }
    
    /**
     * Método helper para registrar singletons
     */
    protected function singleton(string $abstract, callable $concrete): void {
        $this->container->singleton($abstract, $concrete);
    }
}
