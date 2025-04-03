<?php
include('conex.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $conexion->prepare("SELECT * FROM INFO1170_Reservas WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $reserva = $result->fetch_assoc();
} else {
    die("ID de reserva no especificado.");
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Editar Reserva</h2>
    <form action="procesar_edicion_reserva.php" method="POST" class="p-4 bg-white shadow rounded">
        <input type="hidden" name="id" value="<?= $reserva['id'] ?>">
        <div class="mb-3">
            <label for="evento" class="form-label">Nombre del Evento</label>
            <input type="text" id="evento" name="evento" class="form-control" value="<?= $reserva['evento'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= $reserva['fecha'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora_inicio" class="form-label">Hora de Inicio</label>
            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" value="<?= $reserva['hora_inicio'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora_fin" class="form-label">Hora de Fin</label>
            <input type="time" id="hora_fin" name="hora_fin" class="form-control" value="<?= $reserva['hora_fin'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="zona" class="form-label">Zona de Estacionamiento</label>
            <input type="text" id="zona" name="zona" class="form-control" value="<?= $reserva['zona'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
    </form>
</div>
