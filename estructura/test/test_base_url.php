<?php
/**
 * Archivo de prueba para verificar la detección automática de BASE_URL
 * Este archivo simula diferentes entornos para verificar que la función
 * getBaseUrl() funcione correctamente en distintos escenarios.
 */

// Simular diferentes entornos para probar la función
$testCases = [
    // Caso 1: Servidor de desarrollo local (XAMPP/WAMP)
    [
        'HTTP_HOST' => 'localhost',
        'SCRIPT_NAME' => '/Estacionamientos_UCT/index.php',
        'HTTPS' => '',
        'SERVER_PORT' => 80,
        'expected' => 'http://localhost/Estacionamientos_UCT'
    ],
    
    // Caso 2: Servidor de desarrollo local con puerto personalizado
    [
        'HTTP_HOST' => 'localhost:8080',
        'SCRIPT_NAME' => '/proyecto/estructura/views/login.php',
        'HTTPS' => '',
        'SERVER_PORT' => 8080,
        'expected' => 'http://localhost:8080/proyecto'
    ],
      // Caso 3: Servidor de producción con HTTPS
    [
        'HTTP_HOST' => 'pillan.inf.uct.cl',
        'SCRIPT_NAME' => '/~dprado/Estacionamientos_UCT/estructura/controllers/procesar_login.php',
        'HTTPS' => 'on',
        'SERVER_PORT' => 443,
        'expected' => 'https://pillan.inf.uct.cl/~dprado/Estacionamientos_UCT'
    ],
    
    // Caso 4: Servidor en la raíz del dominio
    [
        'HTTP_HOST' => 'estacionamientos.uct.cl',
        'SCRIPT_NAME' => '/estructura/views/registro_vehiculos.php',
        'HTTPS' => 'on',
        'SERVER_PORT' => 443,
        'expected' => 'https://estacionamientos.uct.cl'
    ],
    
    // Caso 5: Subdominio con carpeta
    [
        'HTTP_HOST' => 'apps.uct.cl',
        'SCRIPT_NAME' => '/estacionamientos/estructura/services/get_parking_spaces.php',
        'HTTPS' => 'on',
        'SERVER_PORT' => 443,
        'expected' => 'https://apps.uct.cl/estacionamientos'
    ]
];

echo "<h1>Prueba de Detección Automática de BASE_URL</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-case { 
        border: 1px solid #ddd; 
        margin: 10px 0; 
        padding: 15px; 
        border-radius: 5px; 
    }
    .success { background-color: #d4edda; border-color: #c3e6cb; }
    .error { background-color: #f8d7da; border-color: #f5c6cb; }
    .info { background-color: #e2e3e5; border-color: #d6d8db; }
    code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
</style>\n";

// Función de prueba que simula getBaseUrl()
function testGetBaseUrl($testEnv) {
    $protocol = (!empty($testEnv['HTTPS']) && $testEnv['HTTPS'] !== 'off' || $testEnv['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $testEnv['HTTP_HOST'];
    
    // Obtener la ruta del script actual
    $scriptName = $testEnv['SCRIPT_NAME'];
    
    // Detectar si estamos en la carpeta estructura o en la raíz
    if (strpos($scriptName, '/estructura/') !== false) {
        // Estamos dentro de la carpeta estructura
        $projectPath = substr($scriptName, 0, strpos($scriptName, '/estructura/'));
    } else {
        // Estamos en la raíz del proyecto
        $projectPath = dirname($scriptName);
    }
    
    // Asegurar que no termine con /
    $projectPath = rtrim($projectPath, '/');
    
    // Si la ruta está vacía, usar /
    if (empty($projectPath)) {
        $projectPath = '';
    }
    
    return $protocol . $host . $projectPath;
}

// Ejecutar las pruebas
foreach ($testCases as $index => $testCase) {
    $result = testGetBaseUrl($testCase);
    $isSuccess = ($result === $testCase['expected']);
    
    $cssClass = $isSuccess ? 'success' : 'error';
    
    echo "<div class='test-case $cssClass'>\n";
    echo "<h3>Caso de Prueba " . ($index + 1) . "</h3>\n";
    echo "<strong>Entorno simulado:</strong><br>\n";
    echo "- Host: <code>{$testCase['HTTP_HOST']}</code><br>\n";
    echo "- Script: <code>{$testCase['SCRIPT_NAME']}</code><br>\n";
    echo "- HTTPS: <code>" . ($testCase['HTTPS'] ? 'Sí' : 'No') . "</code><br>\n";
    echo "- Puerto: <code>{$testCase['SERVER_PORT']}</code><br><br>\n";
    
    echo "<strong>Resultado:</strong><br>\n";
    echo "- Esperado: <code>{$testCase['expected']}</code><br>\n";
    echo "- Obtenido: <code>$result</code><br>\n";
    echo "- Estado: <strong>" . ($isSuccess ? '✅ CORRECTO' : '❌ ERROR') . "</strong>\n";
    echo "</div>\n";
}

// Mostrar el entorno actual real
echo "<div class='test-case info'>\n";
echo "<h3>Entorno Actual (Real)</h3>\n";
if (isset($_SERVER['HTTP_HOST'])) {
    echo "<strong>Tu entorno actual:</strong><br>\n";
    echo "- Host: <code>" . ($_SERVER['HTTP_HOST'] ?? 'No disponible') . "</code><br>\n";
    echo "- Script: <code>" . ($_SERVER['SCRIPT_NAME'] ?? 'No disponible') . "</code><br>\n";
    echo "- HTTPS: <code>" . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'Sí' : 'No') . "</code><br>\n";
    echo "- Puerto: <code>" . ($_SERVER['SERVER_PORT'] ?? 'No disponible') . "</code><br><br>\n";
    
    // Incluir el config actual para obtener el BASE_URL real
    include_once 'estructura/config/config.php';
    echo "- BASE_URL detectado: <code>" . BASE_URL . "</code><br>\n";
    echo "- CSS_PATH: <code>" . CSS_PATH . "</code><br>\n";
    echo "- JS_PATH: <code>" . JS_PATH . "</code><br>\n";
} else {
    echo "Este archivo se está ejecutando desde línea de comandos. Para ver el entorno real, ejecuta este archivo desde un navegador web.\n";
}
echo "</div>\n";

echo "<div class='test-case info'>\n";
echo "<h3>Instrucciones de Uso</h3>\n";
echo "<p>Para probar que la detección automática funciona correctamente:</p>\n";
echo "<ol>\n";
echo "<li>Ejecuta este archivo desde tu navegador web</li>\n";
echo "<li>Verifica que todos los casos de prueba sean correctos</li>\n";
echo "<li>Comprueba que el 'Entorno Actual' muestre la URL base correcta</li>\n";
echo "<li>Prueba acceder a algún archivo CSS usando la ruta mostrada en CSS_PATH</li>\n";
echo "</ol>\n";
echo "</div>\n";
?>
