USE a2024_dvasquez;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. TABLA DE VEHÍCULOS (Sistema de Registro)
-- =====================================================

-- Crear tabla de vehículos con estructura completa
CREATE TABLE IF NOT EXISTS vehiculos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    propietario_nombre VARCHAR(100) NOT NULL,
    propietario_apellido VARCHAR(100) NOT NULL,
    propietario_email VARCHAR(150),
    propietario_telefono VARCHAR(20),
    patente VARCHAR(20) UNIQUE NOT NULL,
    tipo ENUM('Auto', 'Camioneta', 'Motocicleta', 'Bicicleta', 'Otro') DEFAULT 'Auto',
    marca VARCHAR(50),
    modelo VARCHAR(50),
    año YEAR,
    color VARCHAR(30),
    zona_autorizada VARCHAR(20) NOT NULL,
    tipo_usuario ENUM('Regular', 'Estudiante', 'Docente', 'Administrativo', 'Visitante') DEFAULT 'Regular',
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_patente (patente),
    INDEX idx_propietario (propietario_nombre, propietario_apellido),
    INDEX idx_zona (zona_autorizada),
    INDEX idx_tipo_usuario (tipo_usuario),
    INDEX idx_activo (activo)
);

-- Tabla de marcas de vehículos
CREATE TABLE IF NOT EXISTS marcas_vehiculos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar marcas básicas
INSERT IGNORE INTO marcas_vehiculos (nombre) VALUES
('Toyota'), ('Honda'), ('Ford'), ('Chevrolet'), ('Nissan'),
('Hyundai'), ('Volkswagen'), ('Kia'), ('Mazda'), ('Subaru'),
('BMW'), ('Mercedes-Benz'), ('Audi'), ('Peugeot'), ('Renault'),
('Fiat'), ('Suzuki'), ('Mitsubishi'), ('Jeep'), ('Land Rover'),
('Volvo'), ('Citroën'), ('SEAT'), ('Skoda'), ('Alfa Romeo');

-- =====================================================
-- 2. TABLA DE RESERVAS (Actualización de la existente)
-- =====================================================

-- Verificar si la tabla de reservas existe y actualizarla
CREATE TABLE IF NOT EXISTS INFO1170_Reservas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evento VARCHAR(255) NOT NULL,
    fecha DATE NOT NULL,
    zona VARCHAR(10) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    patente VARCHAR(20) NOT NULL,
    numero_espacio VARCHAR(20),
    estado ENUM('activa', 'completada', 'cancelada') DEFAULT 'activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_liberacion TIMESTAMP NULL,
    observaciones TEXT
);

-- Agregar columnas nuevas si no existen
ALTER TABLE INFO1170_Reservas 
ADD COLUMN IF NOT EXISTS numero_espacio VARCHAR(20),
ADD COLUMN IF NOT EXISTS estado ENUM('activa', 'completada', 'cancelada') DEFAULT 'activa',
ADD COLUMN IF NOT EXISTS fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS fecha_liberacion TIMESTAMP NULL,
ADD COLUMN IF NOT EXISTS observaciones TEXT;

-- Agregar índices si no existen
ALTER TABLE INFO1170_Reservas 
ADD INDEX IF NOT EXISTS idx_zona_fecha (zona, fecha),
ADD INDEX IF NOT EXISTS idx_horario (hora_inicio, hora_fin),
ADD INDEX IF NOT EXISTS idx_estado (estado),
ADD INDEX IF NOT EXISTS idx_patente (patente),
ADD INDEX IF NOT EXISTS idx_fecha_hora (fecha, hora_inicio);

-- =====================================================
-- 3. SISTEMA DE CONTROL DE CUPOS
-- =====================================================

-- Tabla de configuración de zonas
CREATE TABLE IF NOT EXISTS INFO1170_ConfiguracionZonas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    zona VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    cupo_maximo INT NOT NULL DEFAULT 50,
    cupo_reservado INT DEFAULT 0,
    activa BOOLEAN DEFAULT TRUE,
    color_zona VARCHAR(7) DEFAULT '#007bff',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar configuración inicial de zonas
INSERT IGNORE INTO INFO1170_ConfiguracionZonas (zona, nombre, descripcion, cupo_maximo, color_zona) VALUES
('A', 'Zona A - Administrativa', 'Área destinada para personal administrativo y directivo', 50, '#dc3545'),
('B', 'Zona B - Académica', 'Área para profesores, docentes e investigadores', 75, '#28a745'),
('C', 'Zona C - Deportiva', 'Área cercana a instalaciones deportivas y recreativas', 30, '#ffc107'),
('D', 'Zona D - Visitantes', 'Área para visitantes, padres de familia y externos', 25, '#17a2b8');

-- Tabla de historial de ocupación para estadísticas
CREATE TABLE IF NOT EXISTS INFO1170_HistorialOcupacion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    zona VARCHAR(10) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    espacios_ocupados INT NOT NULL,
    espacios_totales INT NOT NULL,
    porcentaje_ocupacion DECIMAL(5,2) NOT NULL,
    tipo_registro ENUM('automatico', 'manual') DEFAULT 'automatico',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_zona_fecha_hora (zona, fecha, hora),
    INDEX idx_fecha (fecha),
    INDEX idx_porcentaje (porcentaje_ocupacion)
);

-- Tabla de eventos y notificaciones
CREATE TABLE IF NOT EXISTS INFO1170_Eventos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('reserva_creada', 'reserva_cancelada', 'zona_llena', 'espacio_liberado') NOT NULL,
    zona VARCHAR(10),
    usuario VARCHAR(255),
    patente VARCHAR(20),
    mensaje TEXT,
    procesado BOOLEAN DEFAULT FALSE,
    fecha_evento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_procesado TIMESTAMP NULL,
    
    INDEX idx_tipo (tipo),
    INDEX idx_procesado (procesado),
    INDEX idx_fecha (fecha_evento)
);

-- =====================================================
-- 4. VISTAS PARA CONSULTAS OPTIMIZADAS
-- =====================================================

-- Vista para vehículos activos con información completa
CREATE OR REPLACE VIEW vista_vehiculos_activos AS
SELECT 
    v.id,
    v.propietario_nombre,
    v.propietario_apellido,
    CONCAT(v.propietario_nombre, ' ', v.propietario_apellido) as propietario_completo,
    v.propietario_email,
    v.propietario_telefono,
    v.patente,
    v.tipo,
    v.marca,
    v.modelo,
    v.año,
    v.color,
    v.zona_autorizada,
    z.nombre as zona_nombre,
    v.tipo_usuario,
    v.fecha_registro,
    v.fecha_actualizacion
FROM vehiculos v
LEFT JOIN INFO1170_ConfiguracionZonas z ON v.zona_autorizada = z.zona
WHERE v.activo = TRUE;

-- Vista para estadísticas en tiempo real por zona
CREATE OR REPLACE VIEW vista_estadisticas_zonas AS
SELECT 
    z.zona,
    z.nombre,
    z.descripcion,
    z.cupo_maximo,
    z.color_zona,
    COALESCE(COUNT(r.id), 0) as reservas_activas,
    (z.cupo_maximo - COALESCE(COUNT(r.id), 0)) as espacios_disponibles,
    ROUND((COALESCE(COUNT(r.id), 0) / z.cupo_maximo) * 100, 2) as porcentaje_ocupacion,
    CASE 
        WHEN (COALESCE(COUNT(r.id), 0) / z.cupo_maximo) >= 0.9 THEN 'LLENO'
        WHEN (COALESCE(COUNT(r.id), 0) / z.cupo_maximo) >= 0.7 THEN 'OCUPADO'
        WHEN (COALESCE(COUNT(r.id), 0) / z.cupo_maximo) >= 0.3 THEN 'DISPONIBLE'
        ELSE 'LIBRE'
    END as estado_ocupacion,
    z.activa
FROM INFO1170_ConfiguracionZonas z
LEFT JOIN INFO1170_Reservas r ON (
    r.zona = z.zona 
    AND r.estado = 'activa' 
    AND r.fecha = CURDATE()
    AND r.hora_inicio <= CURTIME() 
    AND r.hora_fin > CURTIME()
)
WHERE z.activa = TRUE
GROUP BY z.zona, z.nombre, z.descripcion, z.cupo_maximo, z.color_zona, z.activa;

-- Vista para reservas activas del día
CREATE OR REPLACE VIEW vista_reservas_hoy AS
SELECT 
    r.id,
    r.evento,
    r.zona,
    z.nombre as zona_nombre,
    r.hora_inicio,
    r.hora_fin,
    r.usuario,
    r.patente,
    v.propietario_nombre,
    v.propietario_apellido,
    r.numero_espacio,
    r.estado,
    r.fecha_creacion,
    CASE 
        WHEN r.hora_fin <= CURTIME() THEN 'VENCIDA'
        WHEN r.hora_inicio <= CURTIME() AND r.hora_fin > CURTIME() THEN 'ACTIVA'
        ELSE 'PENDIENTE'
    END as estado_actual
FROM INFO1170_Reservas r
LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
LEFT JOIN vehiculos v ON r.patente = v.patente
WHERE r.fecha = CURDATE() AND r.estado = 'activa'
ORDER BY r.zona, r.hora_inicio;

-- Vista para historial de reservas del usuario
CREATE OR REPLACE VIEW vista_historial_reservas AS
SELECT 
    r.id,
    r.evento,
    r.fecha,
    r.zona,
    z.nombre as zona_nombre,
    r.hora_inicio,
    r.hora_fin,
    r.usuario,
    r.patente,
    v.propietario_completo,
    r.estado,
    r.fecha_creacion,
    r.observaciones
FROM INFO1170_Reservas r
LEFT JOIN INFO1170_ConfiguracionZonas z ON r.zona = z.zona
LEFT JOIN vista_vehiculos_activos v ON r.patente = v.patente
ORDER BY r.fecha DESC, r.hora_inicio DESC;

-- =====================================================
-- 5. PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento para buscar vehículo por patente
DELIMITER //
DROP PROCEDURE IF EXISTS sp_buscar_vehiculo_por_patente;
CREATE PROCEDURE sp_buscar_vehiculo_por_patente(
    IN p_patente VARCHAR(20),
    OUT p_encontrado BOOLEAN,
    OUT p_propietario VARCHAR(201),
    OUT p_zona VARCHAR(20),
    OUT p_tipo_usuario VARCHAR(50)
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    
    SELECT COUNT(*), 
           CONCAT(propietario_nombre, ' ', propietario_apellido),
           zona_autorizada,
           tipo_usuario
    INTO v_count, p_propietario, p_zona, p_tipo_usuario
    FROM vehiculos 
    WHERE patente = UPPER(p_patente) AND activo = TRUE;
    
    SET p_encontrado = (v_count > 0);
END //
DELIMITER ;

-- Procedimiento para verificar disponibilidad de espacios
DELIMITER //
DROP PROCEDURE IF EXISTS sp_verificar_disponibilidad;
CREATE PROCEDURE sp_verificar_disponibilidad(
    IN p_zona VARCHAR(10),
    IN p_fecha DATE,
    IN p_hora_inicio TIME,
    IN p_hora_fin TIME,
    OUT p_disponible BOOLEAN,
    OUT p_espacios_ocupados INT,
    OUT p_espacios_totales INT,
    OUT p_porcentaje_ocupacion DECIMAL(5,2)
)
BEGIN
    DECLARE v_cupo_maximo INT DEFAULT 0;
    DECLARE v_reservas_conflicto INT DEFAULT 0;
    
    -- Obtener cupo máximo de la zona
    SELECT cupo_maximo INTO v_cupo_maximo 
    FROM INFO1170_ConfiguracionZonas 
    WHERE zona = p_zona AND activa = TRUE;
    
    -- Si no existe la zona, usar valor por defecto
    IF v_cupo_maximo IS NULL THEN
        SET v_cupo_maximo = 50;
    END IF;
    
    -- Contar reservas que se solapan en horario
    SELECT COUNT(*) INTO v_reservas_conflicto
    FROM INFO1170_Reservas
    WHERE zona = p_zona 
      AND fecha = p_fecha
      AND estado = 'activa'
      AND (
          (hora_inicio <= p_hora_inicio AND hora_fin > p_hora_inicio) OR
          (hora_inicio < p_hora_fin AND hora_fin >= p_hora_fin) OR
          (hora_inicio >= p_hora_inicio AND hora_fin <= p_hora_fin)
      );
    
    SET p_espacios_ocupados = v_reservas_conflicto;
    SET p_espacios_totales = v_cupo_maximo;
    SET p_porcentaje_ocupacion = ROUND((v_reservas_conflicto / v_cupo_maximo) * 100, 2);
    SET p_disponible = (v_reservas_conflicto < v_cupo_maximo);
END //
DELIMITER ;

-- Procedimiento para obtener estadísticas generales
DELIMITER //
DROP PROCEDURE IF EXISTS sp_estadisticas_generales;
CREATE PROCEDURE sp_estadisticas_generales()
BEGIN
    SELECT 
        'ESTADÍSTICAS GENERALES DEL SISTEMA' as titulo,
        (SELECT COUNT(*) FROM vehiculos WHERE activo = TRUE) as total_vehiculos_registrados,
        (SELECT COUNT(*) FROM INFO1170_Reservas WHERE fecha = CURDATE() AND estado = 'activa') as reservas_hoy,
        (SELECT COUNT(*) FROM INFO1170_ConfiguracionZonas WHERE activa = TRUE) as zonas_activas,
        (SELECT SUM(cupo_maximo) FROM INFO1170_ConfiguracionZonas WHERE activa = TRUE) as capacidad_total,
        (SELECT ROUND(AVG(porcentaje_ocupacion), 2) FROM vista_estadisticas_zonas) as ocupacion_promedio;
        
    -- Estadísticas por zona
    SELECT * FROM vista_estadisticas_zonas;
    
    -- Top 5 usuarios con más reservas
    SELECT 
        usuario,
        COUNT(*) as total_reservas,
        COUNT(CASE WHEN estado = 'activa' THEN 1 END) as reservas_activas
    FROM INFO1170_Reservas 
    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY usuario 
    ORDER BY total_reservas DESC 
    LIMIT 5;
END //
DELIMITER ;

-- Procedimiento para limpiar reservas vencidas
DELIMITER //
DROP PROCEDURE IF EXISTS sp_limpiar_reservas_vencidas;
CREATE PROCEDURE sp_limpiar_reservas_vencidas()
BEGIN
    DECLARE v_reservas_actualizadas INT DEFAULT 0;
    
    -- Marcar como completadas las reservas que ya pasaron
    UPDATE INFO1170_Reservas 
    SET estado = 'completada',
        fecha_liberacion = NOW()
    WHERE estado = 'activa' 
      AND (fecha < CURDATE() OR (fecha = CURDATE() AND hora_fin <= CURTIME()));
    
    SET v_reservas_actualizadas = ROW_COUNT();
    
    -- Registrar evento de limpieza
    INSERT INTO INFO1170_Eventos (tipo, mensaje, procesado) 
    VALUES ('espacio_liberado', CONCAT('Se liberaron ', v_reservas_actualizadas, ' espacios automáticamente'), TRUE);
    
    SELECT CONCAT('Se actualizaron ', v_reservas_actualizadas, ' reservas vencidas') as resultado;
END //
DELIMITER ;

-- =====================================================
-- 6. TRIGGERS PARA AUTOMATIZACIÓN
-- =====================================================

-- Trigger para registrar historial después de insertar reserva
DELIMITER //
DROP TRIGGER IF EXISTS tr_reserva_historial_insert;
CREATE TRIGGER tr_reserva_historial_insert
AFTER INSERT ON INFO1170_Reservas
FOR EACH ROW
BEGIN
    DECLARE v_espacios_ocupados INT DEFAULT 0;
    DECLARE v_cupo_maximo INT DEFAULT 50;
    
    -- Obtener cupo máximo de la zona
    SELECT cupo_maximo INTO v_cupo_maximo
    FROM INFO1170_ConfiguracionZonas 
    WHERE zona = NEW.zona AND activa = TRUE;
    
    -- Contar espacios ocupados en el momento de la reserva
    SELECT COUNT(*) INTO v_espacios_ocupados
    FROM INFO1170_Reservas 
    WHERE zona = NEW.zona 
      AND fecha = NEW.fecha 
      AND estado = 'activa'
      AND hora_inicio <= NEW.hora_inicio 
      AND hora_fin > NEW.hora_inicio;
    
    -- Insertar en historial
    INSERT INTO INFO1170_HistorialOcupacion (
        zona, 
        fecha, 
        hora, 
        espacios_ocupados, 
        espacios_totales, 
        porcentaje_ocupacion,
        tipo_registro
    ) VALUES (
        NEW.zona,
        NEW.fecha,
        NEW.hora_inicio,
        v_espacios_ocupados,
        COALESCE(v_cupo_maximo, 50),
        ROUND((v_espacios_ocupados / COALESCE(v_cupo_maximo, 50)) * 100, 2),
        'automatico'
    );
    
    -- Crear evento de nueva reserva
    INSERT INTO INFO1170_Eventos (tipo, zona, usuario, patente, mensaje, procesado) 
    VALUES ('reserva_creada', NEW.zona, NEW.usuario, NEW.patente, 
            CONCAT('Nueva reserva creada para ', NEW.usuario, ' en zona ', NEW.zona), FALSE);
    
    -- Verificar si la zona está llegando al límite
    IF (v_espacios_ocupados / COALESCE(v_cupo_maximo, 50)) >= 0.9 THEN
        INSERT INTO INFO1170_Eventos (tipo, zona, mensaje, procesado) 
        VALUES ('zona_llena', NEW.zona, 
                CONCAT('Zona ', NEW.zona, ' está llegando al límite de capacidad (', 
                       ROUND((v_espacios_ocupados / COALESCE(v_cupo_maximo, 50)) * 100, 1), '%)'), FALSE);
    END IF;
END //
DELIMITER ;

-- Trigger para registrar cancelación de reserva
DELIMITER //
DROP TRIGGER IF EXISTS tr_reserva_cancelada;
CREATE TRIGGER tr_reserva_cancelada
AFTER UPDATE ON INFO1170_Reservas
FOR EACH ROW
BEGIN
    -- Solo si cambió el estado a cancelada
    IF OLD.estado != 'cancelada' AND NEW.estado = 'cancelada' THEN
        INSERT INTO INFO1170_Eventos (tipo, zona, usuario, patente, mensaje, procesado) 
        VALUES ('reserva_cancelada', NEW.zona, NEW.usuario, NEW.patente, 
                CONCAT('Reserva cancelada por ', NEW.usuario, ' en zona ', NEW.zona), FALSE);
    END IF;
END //
DELIMITER ;

-- =====================================================
-- 7. DATOS DE EJEMPLO Y CONFIGURACIÓN INICIAL
-- =====================================================

-- Insertar vehículos de ejemplo
INSERT IGNORE INTO vehiculos (
    propietario_nombre, propietario_apellido, propietario_email, propietario_telefono,
    patente, tipo, marca, modelo, año, color, zona_autorizada, tipo_usuario
) VALUES 
('Juan Carlos', 'Pérez González', 'juan.perez@uct.cl', '+56912345678', 'ABC123', 'Auto', 'Toyota', 'Corolla', 2020, 'Blanco', 'A', 'Docente'),
('María Elena', 'González Soto', 'maria.gonzalez@uct.cl', '+56987654321', 'XYZ789', 'Auto', 'Honda', 'Civic', 2019, 'Azul', 'B', 'Administrativo'),
('Carlos Alberto', 'López Rojas', 'carlos.lopez@uct.cl', '+56911111111', 'DEF456', 'Camioneta', 'Ford', 'Ranger', 2021, 'Negro', 'C', 'Regular'),
('Ana Patricia', 'Martínez Silva', 'ana.martinez@uct.cl', '+56922222222', 'GHI789', 'Auto', 'Chevrolet', 'Spark', 2018, 'Rojo', 'D', 'Estudiante'),
('Pedro Antonio', 'Silva Morales', 'pedro.silva@uct.cl', '+56933333333', 'JKL012', 'Motocicleta', 'Honda', 'CBR600', 2022, 'Azul', 'A', 'Estudiante');

-- Insertar algunas reservas de ejemplo para hoy
INSERT IGNORE INTO INFO1170_Reservas (
    evento, fecha, zona, hora_inicio, hora_fin, usuario, patente, numero_espacio, estado
) VALUES 
('Clase de Programación Avanzada', CURDATE(), 'A', '08:00:00', '10:00:00', 'Juan Carlos Pérez González', 'ABC123', 'A001', 'activa'),
('Reunión Administrativa Semanal', CURDATE(), 'B', '09:00:00', '11:00:00', 'María Elena González Soto', 'XYZ789', 'B015', 'activa'),
('Entrenamiento Deportivo', CURDATE(), 'C', '14:00:00', '16:00:00', 'Carlos Alberto López Rojas', 'DEF456', 'C008', 'activa'),
('Actividad Estudiantil', CURDATE(), 'D', '15:00:00', '17:00:00', 'Ana Patricia Martínez Silva', 'GHI789', 'D003', 'activa'),
('Taller de Mecánica', CURDATE(), 'A', '16:00:00', '18:00:00', 'Pedro Antonio Silva Morales', 'JKL012', 'A025', 'activa');

-- Configurar evento de limpieza automática (se puede programar con cron)
INSERT INTO INFO1170_Eventos (tipo, mensaje, procesado) 
VALUES ('espacio_liberado', 'Sistema de limpieza automática configurado', TRUE);

-- =====================================================
-- 8. CONFIGURACIÓN FINAL Y VERIFICACIONES
-- =====================================================

-- Habilitar nuevamente las restricciones de clave foránea
SET FOREIGN_KEY_CHECKS = 1;

-- Actualizar estadísticas de las tablas para optimización
ANALYZE TABLE vehiculos;
ANALYZE TABLE INFO1170_Reservas;
ANALYZE TABLE INFO1170_ConfiguracionZonas;
ANALYZE TABLE INFO1170_HistorialOcupacion;

-- =====================================================
-- 9. VERIFICACIÓN Y RESUMEN
-- =====================================================

-- Mostrar resumen de lo que se creó
SELECT '🎯 MIGRACIÓN COMPLETADA EXITOSAMENTE' as RESULTADO;

SELECT '📊 RESUMEN DE TABLAS CREADAS:' as INFO;
SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Registros',
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as 'Tamaño_MB'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'a2024_dvasquez' 
    AND TABLE_NAME IN ('vehiculos', 'marcas_vehiculos', 'INFO1170_Reservas', 'INFO1170_ConfiguracionZonas', 'INFO1170_HistorialOcupacion', 'INFO1170_Eventos')
ORDER BY TABLE_NAME;

SELECT '🏢 CONFIGURACIÓN DE ZONAS:' as INFO;
SELECT zona, nombre, cupo_maximo, activa FROM INFO1170_ConfiguracionZonas;

SELECT '📈 ESTADÍSTICAS INICIALES:' as INFO;
SELECT * FROM vista_estadisticas_zonas;

SELECT '🚗 VEHÍCULOS REGISTRADOS:' as INFO;
SELECT COUNT(*) as total_vehiculos FROM vehiculos WHERE activo = TRUE;

SELECT '📅 RESERVAS DEL DÍA:' as INFO;
SELECT COUNT(*) as reservas_hoy FROM INFO1170_Reservas WHERE fecha = CURDATE() AND estado = 'activa';

COMMIT;

-- =====================================================
-- MIGRACIÓN COMPLETADA ✅
-- =====================================================
-- 
-- ✅ TABLAS CREADAS/ACTUALIZADAS:
-- - vehiculos (registro completo de vehículos)
-- - marcas_vehiculos (catálogo de marcas)
-- - INFO1170_Reservas (sistema de reservas mejorado)
-- - INFO1170_ConfiguracionZonas (configuración de cupos)
-- - INFO1170_HistorialOcupacion (historial para estadísticas)
-- - INFO1170_Eventos (sistema de eventos y notificaciones)
--
-- ✅ VISTAS CREADAS:
-- - vista_vehiculos_activos
-- - vista_estadisticas_zonas
-- - vista_reservas_hoy
-- - vista_historial_reservas
--
-- ✅ PROCEDIMIENTOS ALMACENADOS:
-- - sp_buscar_vehiculo_por_patente
-- - sp_verificar_disponibilidad
-- - sp_estadisticas_generales
-- - sp_limpiar_reservas_vencidas
--
-- ✅ TRIGGERS AUTOMÁTICOS:
-- - tr_reserva_historial_insert
-- - tr_reserva_cancelada
--
-- ✅ DATOS INICIALES:
-- - 25 marcas de vehículos
-- - 4 zonas de estacionamiento configuradas
-- - 5 vehículos de ejemplo
-- - 5 reservas de ejemplo para hoy
--
-- 🚀 SISTEMA LISTO PARA USAR
-- =====================================================
