<?php
/**
 * Gestor de sesiones para el sistema de estacionamientos
 * Este archivo debe ser incluido al principio de cada página que requiera sesiones
 */

// Verificar si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    // Configurar parámetros de cookies seguras
    $current_cookie_params = session_get_cookie_params();
    session_set_cookie_params(
        $current_cookie_params["lifetime"],
        $current_cookie_params["path"],
        $current_cookie_params["domain"],
        $current_cookie_params["secure"], // Mantener el valor actual
        true // HttpOnly flag
    );
    
    // Iniciar sesión
    session_start();
}

// Función para verificar si el usuario está autenticado
function is_authenticated() {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
}

// Función para redirigir si el usuario no está autenticado
function redirect_if_not_authenticated($redirect_url = null) {
    if (!is_authenticated()) {
        try {
            // Usar BASE_URL para construir URLs absolutas
            $url = $redirect_url ?? (defined('BASE_URL') ? BASE_URL . "/estructura/views/inicio.php" : '/estructura/views/inicio.php');
            
            // Registrar la redirección para depuración
            log_redirect_error("Redirecting unauthenticated user to", $url);
            
            // Asegurar que la URL es absoluta
            if (strpos($url, 'http') !== 0 && isset($_SERVER['HTTP_HOST'])) {
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
                $url = $protocol . $_SERVER['HTTP_HOST'] . $url;
                log_redirect_error("URL adjusted to absolute", $url);
            }
            
            // Si no se han enviado cabeceras, usar header()
            if (!headers_sent()) {
                header("Location: " . $url);
                exit();
            } else {
                // Si ya se enviaron cabeceras, usar JavaScript
                echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '";</script>';
                exit();
            }
        } catch (Exception $e) {
            log_redirect_error("Redirect error", $e->getMessage());
            // Fallback a la página de inicio básica
            echo '<script>window.location.href="/estructura/views/inicio.php";</script>';
            exit();
        }
    }
}

// Función para redirigir si el usuario ya está autenticado (útil en páginas de login)
function redirect_if_authenticated($redirect_url = null) {
    if (is_authenticated()) {
        try {
            // Usar BASE_URL para construir URLs absolutas
            $url = $redirect_url ?? (defined('BASE_URL') ? BASE_URL . "/estructura/views/pag_inicio.php" : '/estructura/views/pag_inicio.php');
            
            // Registrar la redirección para depuración
            log_redirect_error("Redirecting authenticated user to", $url);
            
            // Asegurar que la URL es absoluta
            if (strpos($url, 'http') !== 0 && isset($_SERVER['HTTP_HOST'])) {
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
                $url = $protocol . $_SERVER['HTTP_HOST'] . $url;
                log_redirect_error("URL adjusted to absolute", $url);
            }
            
            // Si no se han enviado cabeceras, usar header()
            if (!headers_sent()) {
                header("Location: " . $url);
                exit();
            } else {
                // Si ya se enviaron cabeceras, usar JavaScript
                echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '";</script>';
                exit();
            }
        } catch (Exception $e) {
            log_redirect_error("Redirect error", $e->getMessage());
            // Fallback a la página principal básica
            echo '<script>window.location.href="/estructura/views/pag_inicio.php";</script>';
            exit();
        }
    }
}

// Función para cerrar sesión directamente
function logout_user() {
    // Comprobar si hay una sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Guardar nombre de usuario para el registro
    $username = $_SESSION['usuario'] ?? 'Usuario desconocido';
    
    try {
        // Registrar el cierre de sesión antes de borrar datos
        log_redirect_error("User logged out", $username);
    
        // Destruir todos los datos de sesión
        $_SESSION = array();
        
        // Si se usan cookies de sesión, borrarlas
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Limpiar la cookie phpSESSID para mayor seguridad
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), "", time() - 42000, "/");
        }
        
        return true;
    } catch (Exception $e) {
        // Registrar el error
        log_redirect_error("Error during logout", $e->getMessage());
        return false;
    }
}

// Función para registrar errores de redirección
function log_redirect_error($message, $url) {
    // Crear carpeta de logs si no existe
    $log_dir = dirname(__DIR__) . '/logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    // Registrar el error en un archivo de logs
    $log_file = $log_dir . '/redirect_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}: {$url}" . PHP_EOL;
    
    // Añadir información del servidor para depuración
    $log_message .= "  SERVER_NAME: " . $_SERVER['SERVER_NAME'] . PHP_EOL;
    $log_message .= "  HTTP_HOST: " . $_SERVER['HTTP_HOST'] . PHP_EOL;
    $log_message .= "  REQUEST_URI: " . $_SERVER['REQUEST_URI'] . PHP_EOL;
    $log_message .= "  SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . PHP_EOL;
    $log_message .= "  PHP_SELF: " . $_SERVER['PHP_SELF'] . PHP_EOL;
    $log_message .= "  BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . PHP_EOL;
    $log_message .= "  Headers sent: " . (headers_sent() ? 'Yes' : 'No') . PHP_EOL;
    $log_message .= "-----------------------------------" . PHP_EOL;
    
    // Escribir en el archivo de log
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Regenerar ID de sesión periódicamente (cada 30 minutos) para prevenir ataques de fijación de sesión
if (is_authenticated() && 
    (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 1800)) {
    
    // Guardar datos actuales
    $old_session_data = $_SESSION;
    
    // Regenerar ID de sesión y restaurar datos
    session_regenerate_id(true);
    $_SESSION = $old_session_data;
    $_SESSION['last_regeneration'] = time();
}
?>
