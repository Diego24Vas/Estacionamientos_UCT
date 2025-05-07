<?php
include(VIEWS_PATH . '/components/cabecera.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/estilo_reservas.css" rel="stylesheet">
    <title>Reservas</title>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Reservar Estacionamiento</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow p-4 bg-white rounded">
                <form id="reservaForm" action="procesar_reserva.php" method="POST">
                    <div class="mb-3">
                        <label for="evento" class="form-label">Nombre del Evento</label>
                        <input type="text" id="evento" name="evento" class="form-control" placeholder="Ejemplo: Concierto de Verano" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="hora_fin" class="form-label">Hora de Fin</label>
                        <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Nombre del Usuario</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Tu nombre completo" required>
                    </div>
                    <div class="mb-3">
                        <label for="patente" class="form-label">Patente del Vehículo</label>
                        <input type="text" id="patente" name="patente" class="form-control" placeholder="Ejemplo: ABCD12" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoVehiculo" class="form-label">Tipo de Vehículo</label>
                        <select id="tipoVehiculo" name="tipoVehiculo" class="form-control" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="automovil">Automóvil</option>
                            <option value="camioneta">Camioneta</option>
                            <option value="bus">Bus</option>
                            <option value="moto">Motocicleta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="zona" class="form-label">Zona de Estacionamiento</label>
                        <select id="zona" name="zona" class="form-control" required>
                            <option value="">Seleccione una zona</option>
                            <!-- Las opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <input type="hidden" name="capacidadMaxima" value="1">
                    <button type="submit" class="btn btn-primary w-100">Reservar</button>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow p-4 bg-white rounded">
                <h4 class="text-center">Instrucciones</h4>
                <p>Por favor, asegúrate de llenar todos los campos con la información correcta. No se permitirá reservar la misma zona en la misma fecha y hora.</p>
                <p>Si necesitas ayuda, contáctanos mediante los medios oficiales.</p>
                <img src="../img/mapa.png" alt="Mapa del estacionamiento" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
<div class="row mt-5">
    <!-- Recordatorio de eventos -->
    <div class="col-md-12">
        <div class="card shadow p-4 bg-white rounded">
            <h4 class="text-center">Eventos Reservados</h4>
            <div id="recordatorios">
                <!-- Aquí se cargarán los eventos reservados -->
                <p class="text-center text-muted">No hay eventos reservados actualmente.</p>
            </div>
        </div>
    </div>
</div>
<!-- Modal para editar reserva -->
<div class="modal fade" id="editarReservaModal" tabindex="-1" aria-labelledby="editarReservaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarReservaModalLabel">Editar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarReservaForm">
                    <input type="hidden" id="editarReservaId">
                    <div class="mb-3">
                        <label for="editarEvento" class="form-label">Nombre del Evento</label>
                        <input type="text" id="editarEvento" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarFecha" class="form-label">Fecha</label>
                        <input type="date" id="editarFecha" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarHoraInicio" class="form-label">Hora de Inicio</label>
                        <input type="time" id="editarHoraInicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarHoraFin" class="form-label">Hora de Fin</label>
                        <input type="time" id="editarHoraFin" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarZona" class="form-label">Zona de Estacionamiento</label>
                        <select id="editarZona" class="form-control" required>
                            <option value="">Seleccione una zona</option>
                            <!-- Las opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editarTipoVehiculo" class="form-label">Tipo de Vehículo</label>
                        <select id="editarTipoVehiculo" class="form-control" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="automovil">Automóvil</option>
                            <option value="camioneta">Camioneta</option>
                            <option value="bus">Bus</option>
                            <option value="moto">Motocicleta</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="../js/reservas.js"></script>
</body>
</html>

<?php include('pie.php'); ?>
