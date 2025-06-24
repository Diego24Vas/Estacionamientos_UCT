<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';
require_once SERVICES_PATH . '/logica_estadisticas.php'; // Incluir la lógica de estadísticas
include(VIEWS_PATH . '/components/cabecera.php');

// Verificar autenticación obligatoria
redirect_if_not_authenticated();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/stylesnew.css">
    <script src="<?php echo JS_PATH; ?>/alertas.js"></script>
    <style>
        .custom-alert {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
        }
        .container {
            padding: 20px 15px;
        }
        .alert {
            margin-bottom: 15px;
        }
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .dashboard-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            width: 48%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dashboard-card h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .dashboard-card .card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        footer {
            text-align: center;
            margin-top: 20px;
        }

        /* Estilo adicional */
        .stat-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .stat-card .stat-content div {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .stat-card .stat-content .value {
            color: #007bff;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="mb-4 text-center">Estadísticas de Estacionamiento</h2>

        <div class="dashboard">
            <!-- Contenedor combinado para "Máximo de Ocupación Diaria" y "Promedio Diario de Movimientos" -->
            <div class="dashboard-card">
                <div class="stat-card">
                    <div class="stat-content">
                        <div>Máximo de Ocupación Diaria</div>
                        <div class="value"><?= isset($max_ocupacion['ocupacion']) ? $max_ocupacion['ocupacion'] : 0 ?> vehículos</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div>Promedio Diario de Movimientos</div>
                        <div class="value"><?= isset($promedio_movimientos) ? number_format($promedio_movimientos, 2) : '0.00' ?> vehículos</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard">
            <!-- Total de Entradas y Salidas por Día (últimos 5 días + total semanal) -->
            <div class="dashboard-card">
                <h2>Total de Entradas y Salidas por Semana</h2>
                <canvas id="graficoEntradasSalidas"></canvas>
            </div>

            <!-- Días de mayor actividad -->
            <div class="dashboard-card">
                <h2>Días de Mayor Actividad</h2>
                <canvas id="graficoActividadSemanal"></canvas>
            </div>
        </div>
    </div> <!-- Cerramos el contenedor principal -->

        <!-- Script para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de los días de mayor actividad (por día de la semana)
        const ctxActividadSemanal = document.getElementById('graficoActividadSemanal').getContext('2d');
        const datosActividadSemanal = <?= json_encode(isset($grafico_dia_semana) ? $grafico_dia_semana : []) ?>;
        const labelsActividadSemanal = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const cantidadActividad = labelsActividadSemanal.map(dia => datosActividadSemanal[dia] || 0);

        new Chart(ctxActividadSemanal, {
            type: 'bar',
            data: {
                labels: labelsActividadSemanal,
                datasets: [{
                    label: 'Días de Mayor Actividad',
                    data: cantidadActividad,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Día de la Semana' } },
                    y: { title: { display: true, text: 'Cantidad de Movimientos' } }
                }
            }
        });

        // Gráfico de Entradas y Salidas por Semana (últimos 5 días + total semanal)
        const ctxEntradasSalidas = document.getElementById('graficoEntradasSalidas').getContext('2d');
        const datosEntradasSalidas = <?= json_encode(isset($grafico_entradas_salidas) ? $grafico_entradas_salidas : []) ?>;
        const fechasEntradasSalidas = datosEntradasSalidas.map(item => item.fecha);
        const entradas = datosEntradasSalidas.map(item => item.entradas);
        const salidas = datosEntradasSalidas.map(item => item.salidas);

        // Añadir total semanal
        fechasEntradasSalidas.push('Total Semana');
        entradas.push(<?= isset($total_semanal['entradas_semanales']) ? $total_semanal['entradas_semanales'] : 0 ?>);
        salidas.push(<?= isset($total_semanal['salidas_semanales']) ? $total_semanal['salidas_semanales'] : 0 ?>);

        new Chart(ctxEntradasSalidas, {
            type: 'bar',
            data: {
                labels: fechasEntradasSalidas,
                datasets: [
                    {
                        label: 'Entradas',
                        data: entradas,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Salidas',
                        data: salidas,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Fecha' } },
                    y: { title: { display: true, text: 'Cantidad' } }
                }
            }
        });

    </script>

    <?php include(VIEWS_PATH . '/components/pie.php'); ?> <!-- Pie de página fuera del contenedor principal -->
</body>
</html>