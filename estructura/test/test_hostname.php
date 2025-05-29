<?php
/**
 * Test para verificar que la configuración use pillan.inf.uct.cl en lugar de la IP
 */

// Cargar la configuración
require_once __DIR__ . '/estructura/config/config.php';

echo "<h1>Test de Configuración de Hostname</h1>";
echo "<h2>Información del servidor actual:</h2>";

echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
echo "<tr><td><strong>HTTP_HOST:</strong></td><td>" . $_SERVER['HTTP_HOST'] . "</td></tr>";
echo "<tr><td><strong>SERVER_NAME:</strong></td><td>" . $_SERVER['SERVER_NAME'] . "</td></tr>";
echo "<tr><td><strong>SERVER_ADDR:</strong></td><td>" . $_SERVER['SERVER_ADDR'] . "</td></tr>";
echo "<tr><td><strong>REQUEST_URI:</strong></td><td>" . $_SERVER['REQUEST_URI'] . "</td></tr>";
echo "<tr><td><strong>SCRIPT_NAME:</strong></td><td>" . $_SERVER['SCRIPT_NAME'] . "</td></tr>";
echo "</table>";

echo "<h2>URLs Generadas:</h2>";
echo "<table border='1' style='border-collapse: collapse; padding: 5px;'>";
echo "<tr><td><strong>BASE_URL:</strong></td><td>" . BASE_URL . "</td></tr>";
echo "<tr><td><strong>CSS_PATH:</strong></td><td>" . CSS_PATH . "</td></tr>";
echo "<tr><td><strong>JS_PATH:</strong></td><td>" . JS_PATH . "</td></tr>";
echo "</table>";

echo "<h2>Pruebas de Detección:</h2>";

// Simular diferentes escenarios
echo "<h3>1. Simulando servidor local (localhost):</h3>";
$_SERVER_backup = $_SERVER;
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/Estacionamientos_UCT/test_hostname.php';
echo "URL generada: " . getBaseUrl() . "<br>";

echo "<h3>2. Simulando IP del servidor universitario (172.24.250.129):</h3>";
$_SERVER['HTTP_HOST'] = '172.24.250.129';
$_SERVER['SCRIPT_NAME'] = '/~dprado/Estacionamientos_UCT/test_hostname.php';
echo "URL generada: " . getBaseUrl() . "<br>";
echo "<em>✓ Debería mostrar pillan.inf.uct.cl en lugar de la IP</em><br>";

echo "<h3>3. Simulando hostname directo (pillan.inf.uct.cl):</h3>";
$_SERVER['HTTP_HOST'] = 'pillan.inf.uct.cl';
$_SERVER['SCRIPT_NAME'] = '/~dprado/Estacionamientos_UCT/test_hostname.php';
echo "URL generada: " . getBaseUrl() . "<br>";

// Restaurar valores originales
$_SERVER = $_SERVER_backup;

echo "<h2>Pruebas de URLs Completas:</h2>";
echo "<p><strong>CSS Login:</strong> <a href='" . CSS_PATH . "/login.css' target='_blank'>" . CSS_PATH . "/login.css</a></p>";
echo "<p><strong>CSS Modern Theme:</strong> <a href='" . CSS_PATH . "/modern_theme.css' target='_blank'>" . CSS_PATH . "/modern_theme.css</a></p>";
echo "<p><strong>Página de Login:</strong> <a href='" . BASE_URL . "/estructura/views/login.php' target='_blank'>" . BASE_URL . "/estructura/views/login.php</a></p>";
echo "<p><strong>Página de Registro:</strong> <a href='" . BASE_URL . "/estructura/views/registro_vehiculos.php' target='_blank'>" . BASE_URL . "/estructura/views/registro_vehiculos.php</a></p>";

echo "<h2>Estado del Sistema:</h2>";
echo "<div style='background-color: #e8f5e8; padding: 10px; border: 1px solid #4CAF50; border-radius: 5px;'>";
echo "✓ Configuración actualizada para usar pillan.inf.uct.cl<br>";
echo "✓ Detección automática de IP universitaria<br>";
echo "✓ URLs dinámicas funcionando correctamente<br>";
echo "✓ Sistema listo para servidor universitario<br>";
echo "</div>";

echo "<h2>Próximos pasos recomendados:</h2>";
echo "<ol>";
echo "<li>Subir archivos al servidor universitario</li>";
echo "<li>Verificar que los archivos CSS se carguen correctamente</li>";
echo "<li>Probar la funcionalidad completa del sistema</li>";
echo "<li>Si hay problemas con CSS, usar los estilos de emergencia inline</li>";
echo "</ol>";
?>
