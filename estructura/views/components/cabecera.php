<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome para íconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Archivo CSS de estilos personalizados -->
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/stylesnew.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <!-- Bootstrap CSS desde CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery desde CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <!-- Bootstrap JS desde CDN -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <title>Gestión de Estacionamiento - Universidad</title>
<script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            defaultView: 'month',
            events: [
                {
                    title: 'Evento de prueba',
                    start: '2024-11-25',
                    end: '2024-11-26',
                    color: '#f44336', // Color del evento
                },
                {
                    title: 'Mantenimiento programado',
                    start: '2024-12-05',
                    color: '#ff9800', // Otro color
                }
            ]
        });
    });
</script>

</head>
<body style="background-color: #e9f7fd;">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo BASE_URL; ?>/estructura/img/logo.png" alt="Logo Universidad" class="sidebar-logo">
            <h2>Gestión Estacionamiento</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/pag_inicio.php">
                    <i class="fas fa-home menu-icon"></i> Inicio
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/registro_vehiculos.php">
                    <i class="fas fa-car menu-icon"></i> Registro de Vehículos
                </a>
            </li>            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/modificar_registros_simple.php">
                    <i class="fas fa-edit menu-icon"></i> Modificar Registros
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/ver_historial_vehiculos.php">
                    <i class="fas fa-history menu-icon"></i> Ver Historial
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/estadisticas.php">
                    <i class="fas fa-chart-bar menu-icon"></i> Estadísticas
                </a>
            </li>
            <li>                <a href="<?php echo BASE_URL; ?>/estructura/views/reservas_simple.php">
                    <i class="fas fa-calendar-check menu-icon"></i> Reservas
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php">
                    <i class="fas fa-sign-out-alt menu-icon"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        <header class="bg-primary py-3 text-center">
            <h1 class="text-white">Gestión De Estacionamiento</h1>
        </header>
