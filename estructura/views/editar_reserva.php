<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/classReserva.php';
require_once MODELS_PATH . '/conex.php';

$reserva = new Reserva();
$reserva->id = $_GET['id'];  

$conexion = new Conexion();
$conexion = $conexion->getConexion();

// Obtener la reserva por ID
$reserva_data = $reserva->obtenerReservaPorId($conexion);

// Si se reciben datos para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reserva->evento = $_POST['evento'];
    $reserva->fecha = $_POST['fecha'];
    $reserva->horaInicio = $_POST['horaInicio'];
    $reserva->horaFin = $_POST['horaFin'];
    $reserva->zona = $_POST['zona'];

    // Llamar al método para actualizar la reserva
    $reserva->actualizarReserva($conexion);
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Editar Reserva</h2>
    <form action="<?php echo CONTROLLERS_PATH; ?>/procesar_edicion_reserva.php" method="POST" class="p-4 bg-white shadow rounded">
        <input type="hidden" name="id" value="<?= $reserva_data['id'] ?>">
        <div class="mb-3">
            <label for="evento" class="form-label">Nombre del Evento</label>
            <input type="text" id="evento" name="evento" class="form-control" value="<?= $reserva_data['evento'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= $reserva_data['fecha'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora_inicio" class="form-label">Hora de Inicio</label>
            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" value="<?= $reserva_data['hora_inicio'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora_fin" class="form-label">Hora de Fin</label>
            <input type="time" id="hora_fin" name="hora_fin" class="form-control" value="<?= $reserva_data['hora_fin'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="zona" class="form-label">Zona de Estacionamiento</label>
            <input type="text" id="zona" name="zona" class="form-control" value="<?= $reserva_data['zona'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
    </form>
</div>
