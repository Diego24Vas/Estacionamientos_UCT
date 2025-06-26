<?php
class Router {
    private $routes = [];
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Obtener la parte de la URI que corresponde a la API
        // Buscar '/api' en la URI y tomar lo que viene después
        if (preg_match('#/api(.*)$#', $uri, $matches)) {
            $uri = $matches[1];
        } else {
            // Si no contiene /api, usar la URI completa
            $uri = $uri;
        }
        
        // Si la URI está vacía, es la ruta raíz
        if (empty($uri)) {
            $uri = '/';
        }
        
        // Buscar coincidencia de ruta
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                // Convertir patrones de parámetros a regex
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
                $pattern = '#^' . $pattern . '$#';
                
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Remover el match completo
                    
                    try {
                        if (is_callable($route['handler'])) {
                            $response = call_user_func_array($route['handler'], $matches);
                        } else {
                            [$class, $method] = $route['handler'];
                            $controller = new $class();
                            $response = call_user_func_array([$controller, $method], $matches);
                        }
                        
                        // Enviar respuesta JSON
                        header('Content-Type: application/json');
                        if (is_array($response) || is_object($response)) {
                            echo json_encode($response);
                        } else {
                            echo $response;
                        }
                        return;
                    } catch (Exception $e) {
                        http_response_code(500);
                        header('Content-Type: application/json');
                        echo json_encode([
                            'error' => 'Error en el controlador',
                            'message' => $e->getMessage()
                        ]);
                        return;
                    }
                }
            }
        }
        
        // Si no se encuentra la ruta
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Endpoint no encontrado',
            'uri_requested' => $uri,
            'method' => $method,
            'available_routes' => $this->getAvailableRoutes()
        ]);
    }
    
    private function getAvailableRoutes() {
        $routes = [];
        foreach ($this->routes as $route) {
            $routes[] = $route['method'] . ' ' . $route['path'];
        }
        return $routes;
    }
}
?>