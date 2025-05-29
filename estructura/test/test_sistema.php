<?php
/**
 * Script de prueba para verificar el sistema de estacionamiento
 * Este archivo prueba las conexiones básicas y la funcionalidad principal
 */

echo "<h1>Test del Sistema de Estacionamiento</h1>";
echo "<p>Iniciando verificaciones...</p>";

// Test 1: Verificar carga de configuración
echo "<h2>1. Test de Configuración</h2>";
try {
    require_once 'estructura/config/config.php';
    echo "✅ Configuración cargada correctamente<br>";
    echo "- ROOT_PATH: " . ROOT_PATH . "<br>";
    echo "- BASE_URL: " . BASE_URL . "<br>";
    echo "- MODELS_PATH: " . MODELS_PATH . "<br>";
} catch (Exception $e) {
    echo "❌ Error al cargar configuración: " . $e->getMessage() . "<br>";
}

// Test 2: Verificar conexión a la base de datos
echo "<h2>2. Test de Conexión a Base de Datos</h2>";
try {
    if (isset($conexion)) {
        echo "✅ Conexión a base de datos establecida<br>";
        echo "- Host: " . $host . "<br>";
        echo "- Base de datos: " . $BD . "<br>";
        
        // Verificar que las tablas principales existan
        $tables = [
            'INFO1170_VehiculosRegistrados',
            'INFO1170_EspaciosEstacionamiento',
            'INFO1170_Reservas',
            'INFO1170_HistorialRegistros'
        ];
        
        foreach ($tables as $table) {
            $result = $conexion->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Tabla $table existe<br>";
            } else {
                echo "❌ Tabla $table no encontrada<br>";
            }
        }
    } else {
        echo "❌ No se pudo establecer conexión a la base de datos<br>";
    }
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

// Test 3: Verificar que los archivos principales existan
echo "<h2>3. Test de Archivos Principales</h2>";
$files = [
    'estructura/views/pag_inicio.php',
    'estructura/views/registro_vehiculos.php',
    'estructura/views/reservas.php',
    'estructura/views/modificar_registros_simple.php',
    'estructura/views/components/cabecera.php',
    'estructura/controllers/procesar_patente.php',
    'estructura/controllers/procesar_reserva.php',
    'estructura/services/get_parking_spaces.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file no encontrado<br>";
    }
}

// Test 4: Verificar que los archivos CSS existan
echo "<h2>4. Test de Recursos CSS</h2>";
$css_files = [
    'estructura/views/css/stylesnew.css',
    'estructura/views/css/estilo_inicio.css',
    'estructura/views/css/registro_vehiculos.css',
    'estructura/views/css/estilo_reservas.css'
];

foreach ($css_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file no encontrado<br>";
    }
}

// Test 5: Verificar permisos de archivos
echo "<h2>5. Test de Permisos</h2>";
if (is_readable('estructura/config/config.php')) {
    echo "✅ Archivo de configuración es legible<br>";
} else {
    echo "❌ Problemas de permisos en configuración<br>";
}

if (is_readable('estructura/config/conex.php')) {
    echo "✅ Archivo de conexión es legible<br>";
} else {
    echo "❌ Problemas de permisos en conexión<br>";
}

echo "<h2>🎉 Verificaciones Completadas</h2>";
echo "<p>Si todos los tests muestran ✅, el sistema debería funcionar correctamente.</p>";

// Enlaces de prueba directa
echo "<h2>Enlaces de Prueba Directa</h2>";
echo "<a href='estructura/views/inicio.php' target='_blank'>🔗 Página de Login</a><br>";
echo "<a href='estructura/views/pag_inicio.php' target='_blank'>🔗 Página Principal</a><br>";
echo "<a href='estructura/views/registro_vehiculos.php' target='_blank'>🔗 Registro de Vehículos</a><br>";
echo "<a href='estructura/views/reservas.php' target='_blank'>🔗 Reservas</a><br>";
echo "<a href='estructura/views/modificar_registros_simple.php' target='_blank'>🔗 Modificar Registros</a><br>";
?>
