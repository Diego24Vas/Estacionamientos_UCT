<?php
// Página para modificar registros de vehículos y reservas
require_once dirname(__DIR__) . '/config/config.php';
require_once CONFIG_PATH . '/conex.php';
session_start();

// Procesar edición de vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar_vehiculo') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $patente = strtoupper(trim($_POST['patente']));
    $espacio_estacionamiento = trim($_POST['espacio_estacionamiento']);
    
    $sql = "UPDATE INFO1170_VehiculosRegistrados SET 
            nombre = ?, 
            apellido = ?, 
            patente = ?, 
            espacio_estacionamiento = ? 
            WHERE id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $apellido, $patente, $espacio_estacionamiento, $id);
    
    if ($stmt->execute()) {
        $mensaje = "Vehículo actualizado exitosamente";
    } else {
        $error = "Error al actualizar el vehículo";
    }
}

// Procesar eliminación de vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar_vehiculo') {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM INFO1170_VehiculosRegistrados WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $mensaje = "Vehículo eliminado exitosamente";
    } else {
        $error = "Error al eliminar el vehículo";
    }
}

// Procesar edición de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar_reserva') {
    $id = intval($_POST['id']);
    $evento = trim($_POST['evento']);
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];    $zona = trim($_POST['zona']);
    $usuario = trim($_POST['usuario']);
    $patente = strtoupper(trim($_POST['patente']));
    
    $sql = "UPDATE INFO1170_Reservas SET 
            evento = ?, 
            fecha = ?, 
            hora_inicio = ?, 
            hora_fin = ?, 
            zona = ?, 
            usuario = ?, 
            patente = ? 
            WHERE id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssssi", $evento, $fecha, $hora_inicio, $hora_fin, $zona, $usuario, $patente, $id);
    
    if ($stmt->execute()) {
        $mensaje = "Reserva actualizada exitosamente";
    } else {
        $error = "Error al actualizar la reserva";
    }
}

// Procesar eliminación de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'eliminar_reserva') {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM INFO1170_Reservas WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $mensaje = "Reserva eliminada exitosamente";
    } else {
        $error = "Error al eliminar la reserva";
    }
}

// Obtener vehículos
$vehiculos_query = "SELECT * FROM INFO1170_VehiculosRegistrados ORDER BY apellido, nombre";
$vehiculos_result = $conexion->query($vehiculos_query);

// Obtener reservas
$reservas_query = "SELECT * FROM INFO1170_Reservas ORDER BY fecha DESC, hora_inicio";
$reservas_result = $conexion->query($reservas_query);

// Incluir cabecera estándar
include(VIEWS_PATH . '/components/cabecera.php');
?>

<!-- Contenido principal -->
<div class="container mt-3">
    <?php
    // Mostrar mensajes de éxito o error
    if (isset($mensaje)) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-check-circle"></i> ' . htmlspecialchars($mensaje);
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span></button></div>';
    }
    
    if (isset($error)) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($error);
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span></button></div>';
    }
    ?>

    <!-- Navegación por tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="vehiculos-tab" data-toggle="tab" href="#vehiculos" role="tab">
                <i class="fas fa-car"></i> Vehículos Registrados
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reservas-tab" data-toggle="tab" href="#reservas" role="tab">
                <i class="fas fa-calendar-check"></i> Reservas Activas
            </a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Tab de Vehículos -->
        <div class="tab-pane fade show active" id="vehiculos" role="tabpanel">
            <div class="card mt-3">
                <div class="card-header" style="background-color: #0d6efd; color: white;">
                    <h4><i class="fas fa-car"></i> Lista de Vehículos Registrados</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Patente</th>
                                    <th>Espacio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($vehiculos_result && $vehiculos_result->num_rows > 0): ?>
                                    <?php while ($vehiculo = $vehiculos_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $vehiculo['id']; ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($vehiculo['apellido']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($vehiculo['patente']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($vehiculo['espacio_estacionamiento']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editarVehiculo(<?php echo htmlspecialchars(json_encode($vehiculo)); ?>)">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="eliminarVehiculo(<?php echo $vehiculo['id']; ?>, '<?php echo htmlspecialchars($vehiculo['patente']); ?>')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay vehículos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab de Reservas -->
        <div class="tab-pane fade" id="reservas" role="tabpanel">
            <div class="card mt-3">
                <div class="card-header" style="background-color: #0d6efd; color: white;">
                    <h4><i class="fas fa-calendar-check"></i> Lista de Reservas</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>ID</th>
                                    <th>Evento</th>
                                    <th>Fecha</th>
                                    <th>Hora Inicio</th>
                                    <th>Hora Fin</th>
                                    <th>Zona</th>
                                    <th>Usuario</th>
                                    <th>Patente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($reservas_result && $reservas_result->num_rows > 0): ?>
                                    <?php while ($reserva = $reservas_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $reserva['id']; ?></td>
                                            <td><?php echo htmlspecialchars($reserva['evento']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['hora_inicio']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['hora_fin']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['zona']); ?></td>
                                            <td><?php echo htmlspecialchars($reserva['usuario']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($reserva['patente']); ?></strong></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="editarReserva(<?php echo htmlspecialchars(json_encode($reserva)); ?>)">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="eliminarReserva(<?php echo $reserva['id']; ?>, '<?php echo htmlspecialchars($reserva['evento']); ?>')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay reservas registradas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar vehículo -->
<div class="modal fade" id="modalEditarVehiculo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0d6efd; color: white;">
                <h5 class="modal-title">Editar Vehículo</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="editar_vehiculo">
                <input type="hidden" name="id" id="vehiculo_id">                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_nombre">Nombre:</label>
                                <input type="text" class="form-control" id="vehiculo_nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_apellido">Apellido:</label>
                                <input type="text" class="form-control" id="vehiculo_apellido" name="apellido" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_patente">Patente:</label>
                                <input type="text" class="form-control" id="vehiculo_patente" name="patente" required maxlength="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehiculo_espacio_estacionamiento">Espacio de Estacionamiento:</label>
                                <input type="text" class="form-control" id="vehiculo_espacio_estacionamiento" name="espacio_estacionamiento" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #0d6efd;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar reserva -->
<div class="modal fade" id="modalEditarReserva" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0d6efd; color: white;">
                <h5 class="modal-title">Editar Reserva</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="editar_reserva">
                <input type="hidden" name="id" id="reserva_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reserva_evento">Evento:</label>
                        <input type="text" class="form-control" id="reserva_evento" name="evento" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reserva_fecha">Fecha:</label>
                                <input type="date" class="form-control" id="reserva_fecha" name="fecha" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reserva_hora_inicio">Hora Inicio:</label>
                                <input type="time" class="form-control" id="reserva_hora_inicio" name="hora_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reserva_hora_fin">Hora Fin:</label>
                                <input type="time" class="form-control" id="reserva_hora_fin" name="hora_fin" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reserva_zona">Zona:</label>
                                <select class="form-control" id="reserva_zona" name="zona" required>
                                    <option value="A">Zona A</option>
                                    <option value="B">Zona B</option>
                                    <option value="C">Zona C</option>
                                    <option value="D">Zona D</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reserva_usuario">Usuario:</label>
                                <input type="text" class="form-control" id="reserva_usuario" name="usuario" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reserva_patente">Patente:</label>
                        <input type="text" class="form-control" id="reserva_patente" name="patente" required maxlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #0d6efd;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formularios ocultos para eliminación -->
<form id="formEliminarVehiculo" method="POST" style="display: none;">
    <input type="hidden" name="action" value="eliminar_vehiculo">
    <input type="hidden" name="id" id="eliminar_vehiculo_id">
</form>

<form id="formEliminarReserva" method="POST" style="display: none;">
    <input type="hidden" name="action" value="eliminar_reserva">
    <input type="hidden" name="id" id="eliminar_reserva_id">
</form>

</div>

<script>
// Función para editar vehículo
function editarVehiculo(vehiculo) {
    document.getElementById('vehiculo_id').value = vehiculo.id;
    document.getElementById('vehiculo_nombre').value = vehiculo.nombre;
    document.getElementById('vehiculo_apellido').value = vehiculo.apellido;
    document.getElementById('vehiculo_patente').value = vehiculo.patente;
    document.getElementById('vehiculo_espacio_estacionamiento').value = vehiculo.espacio_estacionamiento;
    
    $('#modalEditarVehiculo').modal('show');
}

// Función para eliminar vehículo
function eliminarVehiculo(id, patente) {
    if (confirm('¿Está seguro de que desea eliminar el vehículo con patente ' + patente + '?')) {
        document.getElementById('eliminar_vehiculo_id').value = id;
        document.getElementById('formEliminarVehiculo').submit();
    }
}

// Función para editar reserva
function editarReserva(reserva) {
    document.getElementById('reserva_id').value = reserva.id;
    document.getElementById('reserva_evento').value = reserva.evento;
    document.getElementById('reserva_fecha').value = reserva.fecha;
    document.getElementById('reserva_hora_inicio').value = reserva.hora_inicio;
    document.getElementById('reserva_hora_fin').value = reserva.hora_fin;
    document.getElementById('reserva_zona').value = reserva.zona;
    document.getElementById('reserva_usuario').value = reserva.usuario;
    document.getElementById('reserva_patente').value = reserva.patente;
    
    $('#modalEditarReserva').modal('show');
}

// Función para eliminar reserva
function eliminarReserva(id, evento) {
    if (confirm('¿Está seguro de que desea eliminar la reserva "' + evento + '"?')) {
        document.getElementById('eliminar_reserva_id').value = id;
        document.getElementById('formEliminarReserva').submit();
    }
}

// Auto-mayúsculas para patentes
document.getElementById('vehiculo_patente').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

document.getElementById('reserva_patente').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});

// Auto-cerrar alertas después de 5 segundos
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>

</body>
</html>

<?php
$conexion->close();
?>
