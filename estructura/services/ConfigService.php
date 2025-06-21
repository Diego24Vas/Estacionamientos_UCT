<?php

/**
 * Config Service
 * Maneja la configuración de la aplicación usando DI
 */
class ConfigService {
    private array $config = [];
    private bool $loaded = false;
    
    public function __construct() {
        $this->loadConfig();
    }
    
    /**
     * Cargar configuración desde archivos
     */
    private function loadConfig(): void {
        if ($this->loaded) {
            return;
        }
        
        // Cargar configuración base
        $configPath = dirname(__DIR__) . '/config/config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            
            // Convertir constantes a array de configuración
            $this->config = [
                'paths' => [
                    'base' => defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__),
                    'views' => defined('VIEWS_PATH') ? VIEWS_PATH : dirname(__DIR__) . '/views',
                    'controllers' => defined('CONTROLLERS_PATH') ? CONTROLLERS_PATH : dirname(__DIR__) . '/controllers',
                    'services' => defined('SERVICES_PATH') ? SERVICES_PATH : dirname(__DIR__) . '/services',
                    'models' => defined('MODELS_PATH') ? MODELS_PATH : dirname(__DIR__) . '/models',
                    'css' => defined('CSS_PATH') ? CSS_PATH : '/css',
                    'js' => defined('JS_PATH') ? JS_PATH : '/js',
                    'img' => defined('IMG_PATH') ? IMG_PATH : '/img'
                ],
                'urls' => [
                    'base' => defined('BASE_URL') ? BASE_URL : 'http://localhost/Estacionamientos_UCT/estructura'
                ],
                'app' => [
                    'name' => 'Sistema de Estacionamientos UCT',
                    'version' => '1.0.0',
                    'timezone' => 'America/Santiago'
                ]
            ];
        }
        
        $this->loaded = true;
    }
    
    /**
     * Obtener un valor de configuración
     */
    public function get(string $key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Establecer un valor de configuración
     */
    public function set(string $key, $value): void {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
    
    /**
     * Verificar si existe una configuración
     */
    public function has(string $key): bool {
        return $this->get($key) !== null;
    }
    
    /**
     * Obtener toda la configuración
     */
    public function all(): array {
        return $this->config;
    }
    
    /**
     * Obtener configuración de paths
     */
    public function getPath(string $path): string {
        return $this->get("paths.{$path}", '');
    }
    
    /**
     * Obtener configuración de URLs
     */
    public function getUrl(string $url): string {
        return $this->get("urls.{$url}", '');
    }
}
