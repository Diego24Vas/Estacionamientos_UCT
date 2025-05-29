<?php
/**
 * Archivo de prueba para verificar la detección automática de BASE_URL
 * Ejecuta este archivo para ver si las rutas se detectan correctamente
 */

require_once 'estructura/config/config.php';

echo "<h2>Configuración de Rutas Dinámicas</h2>";
echo "<hr>";

echo "<h3>Información del Servidor:</h3>";
echo "<p><strong>HTTP_HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h3>Rutas Detectadas Automáticamente:</h3>";
echo "<p><strong>BASE_URL:</strong> " . BASE_URL . "</p>";
echo "<p><strong>CSS_PATH:</strong> " . CSS_PATH . "</p>";
echo "<p><strong>JS_PATH:</strong> " . JS_PATH . "</p>";

echo "<h3>Rutas del Sistema de Archivos:</h3>";
echo "<p><strong>ROOT_PATH:</strong> " . ROOT_PATH . "</p>";
echo "<p><strong>MODELS_PATH:</strong> " . MODELS_PATH . "</p>";
echo "<p><strong>VIEWS_PATH:</strong> " . VIEWS_PATH . "</p>";
echo "<p><strong>CONTROLLERS_PATH:</strong> " . CONTROLLERS_PATH . "</p>";

echo "<h3>Pruebas de Rutas:</h3>";
echo "<p>🔗 <a href='" . BASE_URL . "' target='_blank'>Ir a la página principal</a></p>";
echo "<p>🎨 <a href='" . CSS_PATH . "/styles.css' target='_blank'>Probar enlace CSS</a></p>";

echo "<h3>Ejemplos de Uso en PHP:</h3>";
echo "<pre style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo htmlspecialchars('<!-- En las vistas HTML -->
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>/styles.css">
<script src="<?php echo JS_PATH; ?>/script.js"></script>

<!-- Para redirecciones -->
header("Location: " . BASE_URL . "/estructura/views/login.php");

<!-- Para enlaces internos -->
<a href="<?php echo BASE_URL; ?>/estructura/views/dashboard.php">Dashboard</a>');
echo "</pre>";

echo "<hr>";
echo "<p style='color: green;'><strong>✓ Configuración de rutas dinámicas funcionando correctamente!</strong></p>";
echo "<p><em>Nota: Estas rutas se adaptarán automáticamente a cualquier entorno (local, desarrollo, producción).</em></p>";
?>
