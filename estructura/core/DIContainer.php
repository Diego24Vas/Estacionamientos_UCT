<?php

/**
 * Dependency Injection Container
 * Maneja el registro y resolución de dependencias
 */
class DIContainer {
    private $services = [];
    private $singletons = [];
    private $instances = [];
    
    /**
     * Registra un servicio en el container
     */
    public function register(string $name, callable $factory, bool $singleton = false): void {
        $this->services[$name] = $factory;
        if ($singleton) {
            $this->singletons[$name] = true;
        }
    }
    
    /**
     * Registra un servicio como singleton
     */
    public function singleton(string $name, callable $factory): void {
        $this->register($name, $factory, true);
    }
    
    /**
     * Resuelve y retorna una instancia del servicio
     */
    public function get(string $name) {
        // Si es singleton y ya existe una instancia, retornarla
        if (isset($this->singletons[$name]) && isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        
        // Si no existe el servicio, lanzar excepción
        if (!isset($this->services[$name])) {
            throw new Exception("Service '{$name}' not found in container");
        }
        
        // Crear nueva instancia
        $factory = $this->services[$name];
        $instance = $factory($this);
        
        // Si es singleton, guardar la instancia
        if (isset($this->singletons[$name])) {
            $this->instances[$name] = $instance;
        }
        
        return $instance;
    }
    
    /**
     * Verifica si un servicio está registrado
     */
    public function has(string $name): bool {
        return isset($this->services[$name]);
    }
    
    /**
     * Registra una instancia ya creada
     */
    public function instance(string $name, $instance): void {
        $this->instances[$name] = $instance;
        $this->singletons[$name] = true;
    }
    
    /**
     * Auto-wire - Resuelve dependencias automáticamente usando reflection
     */
    public function make(string $className) {
        $reflector = new ReflectionClass($className);
        
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$className} is not instantiable");
        }
        
        $constructor = $reflector->getConstructor();
        
        if (is_null($constructor)) {
            return new $className;
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            
            if ($dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve class dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->make($dependency->name);
            }
        }
        
        return $reflector->newInstanceArgs($dependencies);
    }
}
