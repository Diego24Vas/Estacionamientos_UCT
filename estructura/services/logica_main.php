<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';

// Redirigir si no está autenticado
redirect_if_not_authenticated();

// Incluir la conexión a la base de datos
require_once CONFIG_PATH . '/conex.php';

// Resumen de espacios
$query_estacionamiento = "
    SELECT 
        COUNT(*) AS total, 
        SUM(CASE WHEN Estado = 'Disponible' THEN 1 ELSE 0 END) AS libres, 
        SUM(CASE WHEN Estado = 'Ocupado' THEN 1 ELSE 0 END) AS ocupados 
    FROM INFO1170_Estacionamiento";
$result_estacionamiento = $conexion->query($query_estacionamiento);
$data_estacionamiento = $result_estacionamiento->fetch_assoc();

// Historial de registros
$query_historial = "
    SELECT 
        hr.IdVehiculo, 
        vr.patente, 
        vr.espacio_estacionamiento, 
        hr.fecha, 
        hr.accion
    FROM 
        INFO1170_HistorialRegistros hr
    JOIN 
        INFO1170_VehiculosRegistrados vr ON hr.IdVehiculo = vr.id
    ORDER BY 
        hr.fecha DESC
    LIMIT 10";
$result_historial = $conexion->query($query_historial);

// Datos para el gráfico
$query_grafico = "
    SELECT 
        DATE(fecha) AS fecha, 
        SUM(CASE WHEN accion = 'Entrada' THEN 1 ELSE 0 END) AS entradas, 
        SUM(CASE WHEN accion = 'Salida' THEN 1 ELSE 0 END) AS salidas 
    FROM INFO1170_HistorialRegistros 
    GROUP BY DATE(fecha)";
$result_grafico = $conexion->query($query_grafico);

$grafico_datos = [];
while ($row = $result_grafico->fetch_assoc()) {
    $grafico_datos[] = $row;
}
?>
