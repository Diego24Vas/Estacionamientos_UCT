<?php 
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';

// Verificar autenticación obligatoria
redirect_if_not_authenticated();

require_once CONFIG_PATH . '/conex.php';
session_start();

include(VIEWS_PATH . '/components/cabecera.php'); 

// Procesar formulario de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear_reserva') {
    $evento = trim($_POST['evento']);
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $zona = trim($_POST['zona']);
    $usuario = trim($_POST['usuario']);
    $patente = strtoupper(trim($_POST['patente']));
    
    $sql = "INSERT INTO INFO1170_Reservas (evento, fecha, hora_inicio, hora_fin, zona, usuario, patente) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssss", $evento, $fecha, $hora_inicio, $hora_fin, $zona, $usuario, $patente);
    
    if ($stmt->execute()) {
        $mensaje = "Reserva creada exitosamente";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al crear la reserva: " . $conexion->error;
        $tipo_mensaje = "danger";
    }
}

// Obtener reservas existentes
$reservas_query = "SELECT * FROM INFO1170_Reservas ORDER BY fecha DESC, hora_inicio";
$reservas_result = $conexion->query($reservas_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Estacionamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/stylesnew.css">
    <style>
        .container {
            padding: 20px 15px;
        }
        .alert {
            margin-bottom: 15px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
        }
        .form-label {
            font-weight: 500;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo ($tipo_mensaje === 'success') ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2 class="text-center mb-4">Sistema de Reservas de Estacionamiento</h2>

    <div class="row">
        <!-- Formulario de Nueva Reserva -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i> Nueva Reserva
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="crear_reserva">
                        
                        <div class="mb-3">
                            <label for="evento" class="form-label">Nombre del Evento</label>
                            <input type="text" class="form-control" id="evento" name="evento" 
                                   placeholder="Ejemplo: Reunión de trabajo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_inicio" class="form-label">Hora Inicio</label>
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_fin" class="form-label">Hora Fin</label>
                                    <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="zona" class="form-label">Zona de Estacionamiento</label>
                            <select class="form-control" id="zona" name="zona" required>
                                <option value="">Seleccione una zona</option>
                                <option value="A">Zona A</option>
                                <option value="B">Zona B</option>
                                <option value="C">Zona C</option>
                                <option value="D">Zona D</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Nombre del Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                   placeholder="Su nombre completo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="patente" class="form-label">Patente del Vehículo</label>
                            <input type="text" class="form-control" id="patente" name="patente" 
                                   placeholder="Ejemplo: ABCD12" maxlength="8" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Crear Reserva
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información y Mapa -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Información
                </div>
                <div class="card-body">
                    <h5>Instrucciones para Reservar</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Complete todos los campos obligatorios</li>
                        <li><i class="fas fa-check text-success"></i> Verifique que la fecha sea futura</li>
                        <li><i class="fas fa-check text-success"></i> La hora de fin debe ser posterior a la de inicio</li>
                        <li><i class="fas fa-check text-success"></i> No se permite reservar la misma zona en horarios superpuestos</li>
                    </ul>
                    
                    <div class="mt-4">
                        <h6>Zonas Disponibles:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Zona A:</strong> Entrada principal</li>
                            <li><strong>Zona B:</strong> Lateral izquierdo</li>
                            <li><strong>Zona C:</strong> Lateral derecho</li>
                            <li><strong>Zona D:</strong> Fondo del estacionamiento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Reservas Existentes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-check"></i> Reservas Activas
                </div>
                <div class="card-body">
                    <?php if ($reservas_result && $reservas_result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Zona</th>
                                        <th>Usuario</th>
                                        <th>Patente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($reserva = $reservas_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reserva['evento']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['hora_inicio']); ?> - <?php echo htmlspecialchars($reserva['hora_fin']); ?></td>
                                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($reserva['zona']); ?></span></td>
                                            <td><?php echo htmlspecialchars($reserva['usuario']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($reserva['patente']); ?></strong></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>No hay reservas registradas actualmente</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-mayúsculas para patente
document.getElementById('patente').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Validación de fecha (no permitir fechas pasadas)
document.getElementById('fecha').addEventListener('change', function(e) {
    const fechaSeleccionada = new Date(e.target.value);
    const fechaHoy = new Date();
    fechaHoy.setHours(0, 0, 0, 0);
    
    if (fechaSeleccionada < fechaHoy) {
        alert('No se puede reservar para fechas pasadas');
        e.target.value = '';
    }
});

// Validación de horas
document.getElementById('hora_fin').addEventListener('change', function(e) {
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = e.target.value;
    
    if (horaInicio && horaFin && horaFin <= horaInicio) {
        alert('La hora de fin debe ser posterior a la hora de inicio');
        e.target.value = '';
    }
});

// Auto-cerrar alertas después de 5 segundos
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Establecer fecha mínima como hoy
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    const hoy = new Date().toISOString().split('T')[0];
    fechaInput.setAttribute('min', hoy);
});
</script>

</body>
</html>

<?php
include(VIEWS_PATH . '/components/pie.php');
$conexion->close();
?>
