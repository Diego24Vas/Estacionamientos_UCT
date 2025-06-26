<?php
// Inicializar la aplicación con Dependency Injection
require_once dirname(__DIR__) . '/core/Application.php';

// Verificar autenticación obligatoria antes de cualquier otra operación
require_once dirname(__DIR__) . '/services/session_manager.php';
redirect_if_not_authenticated();

// Inicializar el contenedor DI
$app = Application::getInstance();

// Obtener servicios a través del DI
$configService = $app->get('service.config');
$sessionManager = $app->get('service.session');
$notificationService = $app->get('service.notification');
$viewHelper = $app->get('service.view');

// Configuración tradicional para compatibilidad (se mantiene por ahora)
require_once dirname(__DIR__) . '/config/config.php';
require_once CONFIG_PATH . '/conex.php';

// La sesión ya está manejada por SessionManager
// session_start(); // Ya no necesario

include(VIEWS_PATH . '/components/cabecera.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo $viewHelper->url('css/estilo_reservas.css'); ?>" rel="stylesheet">
    <link href="<?php echo $viewHelper->url('css/stylesnew.css'); ?>" rel="stylesheet">
    <title>Gestión de Reservas</title>
    <style>
        .validation-success { color: #28a745; }
        .validation-error { color: #dc3545; }
        .validation-warning { color: #ffc107; }
        .card-reserva {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .card-reserva:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-actions {
            display: flex;
            gap: 0.5rem;
        }        .patente-status {
            font-size: 0.9em;
            margin-top: 0.25rem;
        }
        .availability-indicator {
            margin-top: 0.5rem;
            font-size: 0.9em;
        }
        .availability-indicator.checking {
            color: #6c757d;
        }
        .availability-indicator.available {
            color: #28a745;
        }
        .availability-indicator.full {
            color: #dc3545;
        }
        .availability-indicator.error {
            color: #ffc107;
        }
        .parking-stats .card {
            transition: transform 0.2s;
        }
        .parking-stats .card:hover {
            transform: translateY(-1px);
        }
        .border-success { border-color: #28a745 !important; }
        .border-info { border-color: #17a2b8 !important; }
        .border-warning { border-color: #ffc107 !important; }
        .border-danger { border-color: #dc3545 !important; }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Estilos adicionales para modo oscuro */
        .modo-oscuro .card-reserva:hover {
            box-shadow: 0 4px 8px rgba(3,102,214,0.3) !important;
        }
        
        .modo-oscuro .availability-indicator.checking {
            color: #aaa !important;
        }
        
        .modo-oscuro .availability-indicator.available {
            color: #28a745 !important;
        }
        
        .modo-oscuro .availability-indicator.full {
            color: #dc3545 !important;
        }
        
        .modo-oscuro .availability-indicator.error {
            color: #ffc107 !important;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <?php
    // Mostrar mensajes usando NotificationService
    echo $notificationService->renderNotifications();
    
    // Compatibilidad con mensajes por URL (para transición gradual)
    if (isset($_GET['mensaje'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-check-circle"></i> ' . htmlspecialchars($_GET['mensaje']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_GET['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    ?><h2 class="text-center mb-4"><i class="fas fa-parking"></i> Gestión de Reservas de Estacionamiento</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow p-4 bg-white rounded">
                <h4 class="text-center mb-4"><i class="fas fa-plus-circle"></i> Nueva Reserva</h4>                <form id="reservaForm" action="<?php echo $viewHelper->url('controllers/procesar_reserva.php'); ?>" method="POST">
                    <?php echo $viewHelper->csrfField(); ?>
                    <div class="mb-3">
                        <label for="evento" class="form-label"><i class="fas fa-calendar-alt"></i> Nombre del Evento</label>
                        <input type="text" id="evento" name="evento" class="form-control" placeholder="Ejemplo: Concierto de Verano" required>
                        <div class="form-text">Ingrese un nombre descriptivo para el evento</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label"><i class="fas fa-calendar"></i> Fecha</label>
                                <input type="date" id="fecha" name="fecha" class="form-control" required min="<?php echo $viewHelper->getMinDate(); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="zona" class="form-label"><i class="fas fa-map-marker-alt"></i> Zona</label>
                                <select id="zona" name="zona" class="form-control" required>
                                    <?php echo $viewHelper->renderZonaOptions(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label"><i class="fas fa-clock"></i> Hora de Inicio</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_fin" class="form-label"><i class="fas fa-clock"></i> Hora de Fin</label>
                                <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="usuario" class="form-label"><i class="fas fa-user"></i> Nombre del Usuario</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Tu nombre completo" required>
                        <div class="form-text">Ingrese el nombre completo del responsable</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="patente" class="form-label"><i class="fas fa-car"></i> Patente del Vehículo</label>
                        <input type="text" id="patente" name="patente" class="form-control" placeholder="Ejemplo: ABCD12" required maxlength="8" style="text-transform: uppercase;">
                        <div id="patente-validation" class="patente-status"></div>
                        <div class="form-text">La patente debe estar registrada en el sistema</div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Crear Reserva
                    </button>
                </form>
            </div>        </div>        <div class="col-md-6">
            <div class="card shadow p-4 bg-white rounded">
                <h4 class="text-center"><i class="fas fa-info-circle"></i> Instrucciones y Mapa</h4>
                <div class="alert alert-info">
                    <h6><i class="fas fa-exclamation-triangle"></i> Importante:</h6>
                    <ul class="mb-0">
                        <li>La patente debe estar registrada en el sistema</li>
                        <li>No se permite reservar en fechas pasadas</li>
                        <li>No se permiten solapamientos de horarios en la misma zona</li>
                        <li>La hora de fin debe ser posterior a la hora de inicio</li>
                    </ul>
                </div>
                <p>Para registrar un nuevo vehículo, diríjase al módulo de <strong>Registro de Vehículos</strong>.</p>
                <img src="<?php echo $viewHelper->url('img/mapa.png'); ?>" alt="Mapa del estacionamiento" class="img-fluid rounded mt-3">
            </div>
        </div>
    </div>
</div>
<div class="row mt-5">
    <div class="col-md-12">
        <div class="card shadow p-4 bg-white rounded">
            <h4 class="text-center mb-4"><i class="fas fa-list"></i> Reservas Activas</h4>
            <div id="recordatorios">
                <!-- Aquí se cargarán los eventos reservados -->
                <div class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Cargando reservas...
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para editar reserva -->
<div class="modal fade" id="editarReservaModal" tabindex="-1" aria-labelledby="editarReservaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editarReservaModalLabel">
                    <i class="fas fa-edit"></i> Editar Reserva
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarReservaForm">
                    <input type="hidden" id="editarReservaId">
                    
                    <div class="mb-3">
                        <label for="editarEvento" class="form-label"><i class="fas fa-calendar-alt"></i> Nombre del Evento</label>
                        <input type="text" id="editarEvento" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">                            <div class="mb-3">
                                <label for="editarFecha" class="form-label"><i class="fas fa-calendar"></i> Fecha</label>
                                <input type="date" id="editarFecha" class="form-control" required min="<?php echo $viewHelper->getMinDate(); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editarZona" class="form-label"><i class="fas fa-map-marker-alt"></i> Zona</label>
                                <select class="form-control" id="editarZona" required>
                                    <?php echo $viewHelper->renderZonaOptions(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editarHoraInicio" class="form-label"><i class="fas fa-clock"></i> Hora de Inicio</label>
                                <input type="time" id="editarHoraInicio" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editarHoraFin" class="form-label"><i class="fas fa-clock"></i> Hora de Fin</label>
                                <input type="time" id="editarHoraFin" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editarUsuario" class="form-label"><i class="fas fa-user"></i> Usuario</label>
                        <input type="text" id="editarUsuario" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editarPatente" class="form-label"><i class="fas fa-car"></i> Patente</label>
                        <input type="text" id="editarPatente" class="form-control" required maxlength="8" style="text-transform: uppercase;">
                        <div id="editarPatente-validation" class="patente-status"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </form>
            </div>
        </div>    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $viewHelper->url('js/reservas.js'); ?>"></script>

<script>
// Configuración DI para JavaScript
window.appConfig = {
    paths: {
        controllers: '<?php echo $viewHelper->url('controllers'); ?>',
        services: '<?php echo $viewHelper->url('services'); ?>',
        base: '<?php echo $configService->getUrl('base'); ?>'
    },
    user: <?php echo json_encode($viewHelper->getCurrentUser() ?: ['id' => null]); ?>,
    csrf: '<?php echo $viewHelper->csrfToken(); ?>'
};

// Helper para mostrar notificaciones desde JavaScript
window.showNotification = function(type, message) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const icon = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle', 
        'info': 'fas fa-info-circle'
    };
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass[type] || 'alert-info'} alert-dismissible fade show`;
    alert.setAttribute('role', 'alert');
    alert.innerHTML = `
        <i class="${icon[type] || 'fas fa-info-circle'}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Insertar al inicio del container
    const container = document.querySelector('.container');
    container.insertBefore(alert, container.firstChild);
    
    // Auto-remove después de 5 segundos
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
};
</script>
</body>
</html>

<?php include(VIEWS_PATH . '/components/pie.php'); ?>
