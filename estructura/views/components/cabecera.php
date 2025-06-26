<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome para íconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS desde CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery COMPLETO (no slim) desde CDN - DEBE IR ANTES DE FullCalendar -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Moment.js para FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <!-- FullCalendar CSS y JS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <!-- Bootstrap JS desde CDN -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <!-- Archivo CSS de estilos personalizados -->
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/stylesnew.css">

    <title>Gestión de Estacionamiento - Universidad</title>
    
<script>
$(document).ready(function() {
    // Inicializar el calendario solo si el elemento existe
    if ($('#calendar').length) {
        $('#calendar').fullCalendar({
            defaultView: 'month',
            events: [
                {
                    title: 'Evento de prueba',
                    start: '2024-11-25',
                    end: '2024-11-26',
                    color: '#f44336',
                },
                {
                    title: 'Mantenimiento programado',
                    start: '2024-12-05',
                    color: '#ff9800',
                }
            ]
        });
    }
    
    // Verificar estado de autenticación cada 5 minutos
    function verificarAutenticacion() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/estructura/controllers/verificar_autenticacion.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.authenticated) {
                    console.log('Sesión expirada o no válida. Redirigiendo...');
                    window.location.href = '<?php echo BASE_URL; ?>/estructura/views/inicio.php';
                } else {
                    console.log('Sesión activa:', data.user.nombre);
                }
            },
            error: function() {
                console.error('Error al verificar autenticación');
            }
        });
    }
    
    // Verificar autenticación cada 5 minutos (300000 ms)
    setInterval(verificarAutenticacion, 300000);
});
</script>

<?php
// Obtener el nombre del archivo actual para marcar el menú activo
$current_page = basename($_SERVER['PHP_SELF']);
?>

</head>
<body style="background-color: #e9f7fd;">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo BASE_URL; ?>/estructura/img/logo.png" alt="Logo Universidad" class="sidebar-logo">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/pag_inicio.php" <?php echo ($current_page == 'pag_inicio.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-home menu-icon"></i> Inicio
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/registro_vehiculos.php" <?php echo ($current_page == 'registro_vehiculos.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-car menu-icon"></i> Registro de Vehículos
                </a>
            </li>            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/modificar_registros_simple.php" <?php echo ($current_page == 'modificar_registros_simple.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-edit menu-icon"></i> Modificar Registros
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/ver_historial_vehiculos.php" <?php echo ($current_page == 'ver_historial_vehiculos.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-history menu-icon"></i> Ver Historial
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/estructura/views/estadisticas.php" <?php echo ($current_page == 'estadisticas.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-chart-bar menu-icon"></i> Estadísticas
                </a>
            </li>
            <li>                <a href="<?php echo BASE_URL; ?>/estructura/views/reservas_simple.php" <?php echo ($current_page == 'reservas_simple.php') ? 'class="active"' : ''; ?>>
                    <i class="fas fa-calendar-check menu-icon"></i> Reservas
                </a>
            </li>
            <!-- Botón de Modo Oscuro -->
            <li>
                <button id="modoOscuroBtn" class="btn-dark-mode">
                    <i class="fas fa-moon"></i> <span id="modoOscuroTexto">Modo Oscuro</span>
                </button>
            </li>            <li>
                <a href="javascript:void(0)" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt menu-icon"></i> Cerrar Sesión
                </a>
            </li>
            
            <script>
            function cerrarSesion() {
                if(confirm('¿Está seguro que desea cerrar sesión?')) {
                    fetch('<?php echo BASE_URL; ?>/estructura/controllers/logout.php')
                    .then(response => {
                        console.log('📡 Respuesta del servidor:', response);
                        if(response.ok || response.redirected) {
                            window.location.href = '<?php echo BASE_URL; ?>/estructura/views/inicio.php';
                        } else {
                            throw new Error('Error en la respuesta del servidor');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error al cerrar sesión:', error);
                        alert('Hubo un problema al cerrar sesión. Por favor, intente nuevamente.');
                        // Redirigir de todas formas como plan B
                        window.location.href = '<?php echo BASE_URL; ?>/estructura/views/inicio.php';
                    });
                }
            }
            </script>
        </ul>
    </div>

    <!-- Script para modo oscuro -->
    <script>
    // Modo Oscuro con persistencia y sin saturar imágenes
    function activarModoOscuro() {
        document.body.classList.add('modo-oscuro');
        document.querySelectorAll('*').forEach(function(el) {
            if (el.tagName !== 'IMG') {
                el.classList.add('letra-blanca');
            }
        });
        document.getElementById('modoOscuroTexto').textContent = 'Modo Claro';
        document.getElementById('modoOscuroBtn').querySelector('i').className = 'fas fa-sun';
    }
    function desactivarModoOscuro() {
        document.body.classList.remove('modo-oscuro');
        document.querySelectorAll('.letra-blanca').forEach(function(el) {
            el.classList.remove('letra-blanca');
        });
        document.getElementById('modoOscuroTexto').textContent = 'Modo Oscuro';
        document.getElementById('modoOscuroBtn').querySelector('i').className = 'fas fa-moon';
    }
    function actualizarModoOscuro() {
        if (localStorage.getItem('modoOscuro') === 'true') {
            activarModoOscuro();
        } else {
            desactivarModoOscuro();
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        actualizarModoOscuro();
        document.getElementById('modoOscuroBtn').addEventListener('click', function() {
            const activado = localStorage.getItem('modoOscuro') === 'true';
            localStorage.setItem('modoOscuro', !activado);
            actualizarModoOscuro();
        });
    });
    </script>
    <!-- Contenido principal -->
    <div class="main-content">
        <header class="bg-primary py-3 text-center">
            <h1 class="text-white">Gestión De Estacionamiento</h1>
        </header>
