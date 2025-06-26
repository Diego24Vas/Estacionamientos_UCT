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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .container h2 {
            color: #212529;
            font-weight: 700;
        }
        
        .modo-oscuro .container h2 {
            color: #ffffff;
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e1e5e9;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .dashboard-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        /* Modo oscuro para tarjetas de gráficos */
        .modo-oscuro .dashboard-card {
            background: #2d3748;
            border-color: #4a5568;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        
        .modo-oscuro .dashboard-card h2 {
            color: #ffffff;
            border-bottom-color: #4a5568;
        }
        
        .table-card {
            background: #ffffff;
            border: 1px solid #e1e5e9;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-top: 20px;
        }
        
        .modo-oscuro .table-card {
            background: #2d3748;
            border-color: #4a5568;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
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

        /* Estilos modernos para las tarjetas de estadísticas */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e1e5e9;
            border-radius: 12px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #0056b3);
        }

        .stat-card .stat-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-card .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .stat-card .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin: 8px 0;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 0;
        }

        /* Modo oscuro para las tarjetas */
        .modo-oscuro .stat-card {
            background: #2d3748;
            border-color: #4a5568;
        }

        .modo-oscuro .stat-card .stat-title {
            color: #a0aec0;
        }

        .modo-oscuro .stat-card .stat-value {
            color: #ffffff;
        }

        .modo-oscuro .stat-card .stat-label {
            color: #a0aec0;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-card .stat-value {
                font-size: 1.75rem;
            }
            
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 15px 10px;
            }
        }
        
        @media (max-width: 480px) {
            .stat-card .stat-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .stat-card .stat-icon {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="mb-4 text-center">Estadísticas de Estacionamiento</h2>

        <!-- Tarjetas de estadísticas principales -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3 class="stat-title">Máximo de Ocupación Diaria</h3>
                        <div class="stat-value"><?= isset($max_ocupacion['ocupacion']) ? $max_ocupacion['ocupacion'] : 0 ?></div>
                        <p class="stat-label">vehículos</p>
                    </div>
                    <div class="stat-icon">
                        🚗
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <h3 class="stat-title">Promedio Diario de Movimientos</h3>
                        <div class="stat-value"><?= isset($promedio_movimientos) ? number_format($promedio_movimientos, 2) : '0.00' ?></div>
                        <p class="stat-label">vehículos promedio</p>
                    </div>
                    <div class="stat-icon">
                        📊
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