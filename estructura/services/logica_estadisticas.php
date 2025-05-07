<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/conex.php';

// Máximo de ocupación diaria: Día con mayor ocupación (basado en entradas)
$query_max_ocupacion = "
    SELECT 
        DATE(fecha) AS fecha, 
        COUNT(*) AS ocupacion
    FROM INFO1170_HistorialRegistros
    WHERE accion = 'Entrada'
    GROUP BY DATE(fecha)
    ORDER BY ocupacion DESC
    LIMIT 1";
$result_max_ocupacion = $conexion->query($query_max_ocupacion);
$max_ocupacion = $result_max_ocupacion->fetch_assoc();

// Días de mayor actividad: Cuántas entradas por día de la semana
$query_dias_actividad = "
    SELECT 
        DAYOFWEEK(fecha) AS dia_semana, 
        COUNT(*) AS cantidad
    FROM INFO1170_HistorialRegistros
    WHERE accion = 'Entrada'
    GROUP BY dia_semana
    ORDER BY cantidad DESC";
$result_dias_actividad = $conexion->query($query_dias_actividad);

// Contar las entradas por día de la semana
$grafico_dia_semana = [];
while ($row = $result_dias_actividad->fetch_assoc()) {
    // Convertimos el valor de DAYOFWEEK (1=Domingo, 2=Lunes, etc.) a nombre de día
    switch ($row['dia_semana']) {
        case 1: $dia = 'Domingo'; break;
        case 2: $dia = 'Lunes'; break;
        case 3: $dia = 'Martes'; break;
        case 4: $dia = 'Miércoles'; break;
        case 5: $dia = 'Jueves'; break;
        case 6: $dia = 'Viernes'; break;
        case 7: $dia = 'Sábado'; break;
    }
    $grafico_dia_semana[$dia] = $row['cantidad'];
}

// Total de entradas y salidas por día (últimos 5 días de la semana + total semanal)
$query_entradas_salidas = "
    SELECT 
        DATE(fecha) AS fecha, 
        SUM(CASE WHEN accion = 'Entrada' THEN 1 ELSE 0 END) AS entradas, 
        SUM(CASE WHEN accion = 'Salida' THEN 1 ELSE 0 END) AS salidas
    FROM INFO1170_HistorialRegistros
    WHERE fecha >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(fecha)
    ORDER BY fecha DESC";
$result_entradas_salidas = $conexion->query($query_entradas_salidas);
$grafico_entradas_salidas = [];
while ($row = $result_entradas_salidas->fetch_assoc()) {
    $grafico_entradas_salidas[] = $row;
}

// Añadir total semanal
$query_total_semanal = "
    SELECT 
        SUM(CASE WHEN accion = 'Entrada' THEN 1 ELSE 0 END) AS entradas_semanales,
        SUM(CASE WHEN accion = 'Salida' THEN 1 ELSE 0 END) AS salidas_semanales
    FROM INFO1170_HistorialRegistros
    WHERE fecha >= CURDATE() - INTERVAL 6 DAY";
$result_total_semanal = $conexion->query($query_total_semanal);
$total_semanal = $result_total_semanal->fetch_assoc();

// Cálculo del promedio diario de movimientos (entradas + salidas)
$query_promedio = "
    SELECT 
        DATE(fecha) AS fecha, 
        COUNT(*) AS total_movimientos 
    FROM INFO1170_HistorialRegistros
    GROUP BY DATE(fecha)
";
$result_promedio = $conexion->query($query_promedio);

$total_movimientos = 0;
$numero_dias = 0;

while ($row = $result_promedio->fetch_assoc()) {
    $total_movimientos += $row['total_movimientos'];
    $numero_dias++;
}

$promedio_movimientos = $numero_dias > 0 ? round($total_movimientos / $numero_dias) : 0;

// Nueva consulta para Ocupación Promedio por Hora
$query_ocupacion_por_hora = "
    SELECT 
        HOUR(fecha) AS hora, 
        COUNT(*) AS ocupacion
    FROM INFO1170_HistorialRegistros
    WHERE accion = 'Entrada'
    GROUP BY hora
    ORDER BY hora";
$result_ocupacion_por_hora = $conexion->query($query_ocupacion_por_hora);

$ocupacion_por_hora = [];
while ($row = $result_ocupacion_por_hora->fetch_assoc()) {
    $ocupacion_por_hora[$row['hora']] = $row['ocupacion'];
}

?>
