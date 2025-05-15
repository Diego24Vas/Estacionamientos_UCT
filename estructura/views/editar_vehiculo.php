<?php
include(VIEWS_PATH . '/components/cabecera.php');
require_once MODELS_PATH . '/conex.php';
require_once __DIR__ . '/../config/config.php';

// verifica si se pasa un id en la url
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM INFO1170_VehiculosRegistrados WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
    
    if (!$vehicle) {
        echo "<script>alert('Registro no encontrado'); window.location.href='ver_registros_vehiculos.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID no proporcionado'); window.location.href='ver_registros_vehiculos.php';</script>";
    exit;
}

// Procesar la actualización del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $owner_first_name = strtoupper($_POST['owner_first_name']);
    $owner_last_name = strtoupper($_POST['owner_last_name']);
    $vehicle_plate = strtoupper($_POST['vehicle_plate']);
    $parking_space = strtoupper($_POST['parking_space']);

    $update_query = "UPDATE INFO1170_VehiculosRegistrados SET nombre = ?, apellido = ?,  patente = ?, espacio_estacionamiento = ? WHERE id = ?";
    $stmt = $conexion->prepare($update_query);
    $stmt->bind_param("ssssi", $owner_first_name, $owner_last_name, $vehicle_plate, $parking_space, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Registro actualizado exitosamente'); window.location.href='ver_registros_vehiculos.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el registro');</script>";
    }
    $stmt->close();
}

$conexion->close();
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/estilos_footer.css">
<h2 class="text-center my-4">Editar Registro de Vehículo</h2>

<div class="container">
    <form action="" method="POST">
        <label for="owner_first_name">Nombre del propietario:</label>
        <input type="text" id="owner_first_name" name="owner_first_name" 
               value="<?php echo htmlspecialchars($vehicle['nombre']); ?>" 
               style="text-transform: uppercase;" required><br><br>

        <label for="owner_last_name">Apellido del propietario:</label>
        <input type="text" id="owner_last_name" name="owner_last_name" 
               value="<?php echo htmlspecialchars($vehicle['apellido']); ?>" 
               style="text-transform: uppercase;" required><br><br>

        <label for="vehicle_plate">Patente del Vehículo:</label>
        <input type="text" id="vehicle_plate" name="vehicle_plate" 
               value="<?php echo htmlspecialchars($vehicle['patente']); ?>" 
               style="text-transform: uppercase;" required><br><br>

        <label for="parking_space">Espacio de Estacionamiento:</label>
        <input type="text" id="parking_space" name="parking_space" 
               value="<?php echo htmlspecialchars($vehicle['espacio_estacionamiento']); ?>" 
               style="text-transform: uppercase;" required><br><br>

        <input type="submit" value="Actualizar Vehículo" class="btn btn-primary">
    </form>
</div>

<?php include(BASE_URL . '/estructura/views/components/pie.php'); ?>
