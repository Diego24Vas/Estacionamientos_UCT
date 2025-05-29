<?php
// Script para verificar y crear la estructura de la base de datos

require_once('estructura/config/conex.php');

echo "<h1>Verificación y Creación de Estructura de Base de Datos</h1>";

try {
    // Verificar conexión
    echo "<h2>1. Conexión a la Base de Datos</h2>";
    echo "✅ Conectado exitosamente a: " . $host . " - Base de datos: " . $BD . "<br><br>";
    
    // Verificar si existe la tabla reservas
    echo "<h2>2. Verificación de Tabla 'reservas'</h2>";
    $result = $conexion->query("SHOW TABLES LIKE 'reservas'");
    
    if ($result->num_rows > 0) {
        echo "✅ La tabla 'reservas' ya existe<br>";
        
        // Mostrar estructura de la tabla
        echo "<h3>Estructura actual de la tabla 'reservas':</h3>";
        $columns = $conexion->query("DESCRIBE reservas");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
        while ($row = $columns->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
    } else {
        echo "❌ La tabla 'reservas' no existe. Creándola...<br>";
        
        $sql = "CREATE TABLE reservas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evento VARCHAR(255) NOT NULL,
            fecha DATE NOT NULL,
            hora_inicio TIME NOT NULL,
            hora_fin TIME NOT NULL,
            usuario VARCHAR(255) NOT NULL,
            patente VARCHAR(20) NOT NULL,
            tipo_vehiculo VARCHAR(50) NOT NULL,
            zona VARCHAR(100) NOT NULL,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado ENUM('activa', 'cancelada', 'completada') DEFAULT 'activa'
        )";
        
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Tabla 'reservas' creada exitosamente<br>";
        } else {
            echo "❌ Error creando tabla 'reservas': " . $conexion->error . "<br>";
        }
    }
      // Verificar si existe la tabla zonas_estacionamiento
    echo "<h2>3. Verificación de Tabla 'zonas_estacionamiento'</h2>";
    $result = $conexion->query("SHOW TABLES LIKE 'zonas_estacionamiento'");
    
    if ($result->num_rows > 0) {
        echo "✅ La tabla 'zonas_estacionamiento' ya existe<br>";
    } else {
        echo "❌ La tabla 'zonas_estacionamiento' no existe. Creándola...<br>";
        
        $sql = "CREATE TABLE zonas_estacionamiento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            capacidad INT NOT NULL DEFAULT 50,
            tipo ENUM('estudiantes', 'profesores', 'visitantes', 'administrativos') DEFAULT 'estudiantes',
            estado ENUM('activa', 'mantenimiento', 'cerrada') DEFAULT 'activa'
        )";
        
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Tabla 'zonas_estacionamiento' creada exitosamente<br>";
            
            // Insertar datos por defecto
            $zonas_default = [
                ['Zona A - Estudiantes', 50, 'estudiantes'],
                ['Zona B - Profesores', 30, 'profesores'],
                ['Zona C - Visitantes', 20, 'visitantes'],
                ['Zona D - Administrativos', 25, 'administrativos']
            ];
            
            foreach ($zonas_default as $zona) {
                $stmt = $conexion->prepare("INSERT INTO zonas_estacionamiento (nombre, capacidad, tipo) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $zona[0], $zona[1], $zona[2]);
                $stmt->execute();
            }
            echo "✅ Datos por defecto insertados en zonas_estacionamiento<br>";
        } else {
            echo "❌ Error creando tabla 'zonas_estacionamiento': " . $conexion->error . "<br>";
        }
    }
    
    // Verificar si existe la tabla vehiculos
    echo "<h2>4. Verificación de Tabla 'vehiculos'</h2>";
    $result = $conexion->query("SHOW TABLES LIKE 'vehiculos'");
    
    if ($result->num_rows > 0) {
        echo "✅ La tabla 'vehiculos' ya existe<br>";
        
        // Mostrar estructura de la tabla
        echo "<h3>Estructura actual de la tabla 'vehiculos':</h3>";
        $columns = $conexion->query("DESCRIBE vehiculos");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
        while ($row = $columns->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
    } else {
        echo "❌ La tabla 'vehiculos' no existe. Creándola...<br>";
        
        $sql = "CREATE TABLE vehiculos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            propietario_nombre VARCHAR(255) NOT NULL,
            propietario_apellido VARCHAR(255) NOT NULL,
            propietario_email VARCHAR(255) NOT NULL,
            propietario_telefono VARCHAR(20),
            patente VARCHAR(20) NOT NULL UNIQUE,
            tipo ENUM('automovil', 'camioneta', 'moto', 'bus') NOT NULL,
            marca VARCHAR(100) NOT NULL,
            modelo VARCHAR(100) NOT NULL,
            año YEAR,
            color VARCHAR(50),
            zona_autorizada VARCHAR(100) NOT NULL,
            tipo_usuario ENUM('estudiante', 'profesor', 'administrativo', 'visitante') NOT NULL,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo'
        )";
        
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Tabla 'vehiculos' creada exitosamente<br>";
        } else {
            echo "❌ Error creando tabla 'vehiculos': " . $conexion->error . "<br>";
        }
    }
    
    // Mostrar estadísticas
    echo "<h2>4. Estadísticas de la Base de Datos</h2>";
    
    $result = $conexion->query("SELECT COUNT(*) as total FROM reservas");
    $row = $result->fetch_assoc();
    echo "📊 Total de reservas: " . $row['total'] . "<br>";
    
    $result = $conexion->query("SELECT COUNT(*) as total FROM zonas_estacionamiento");
    $row = $result->fetch_assoc();
    echo "📊 Total de zonas: " . $row['total'] . "<br>";
    
    echo "<br><h2>5. Test de Funcionalidad</h2>";
    echo "<p><a href='estructura/services/obtener_zonas.php' target='_blank'>🔗 Test obtener_zonas.php</a></p>";
    echo "<p><a href='estructura/services/obtener_eventos.php' target='_blank'>🔗 Test obtener_eventos.php</a></p>";
    echo "<p><a href='estructura/views/reservas.php' target='_blank'>🔗 Ir a página de reservas</a></p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

$conexion->close();
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>
