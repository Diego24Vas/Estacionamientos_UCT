<?php
require_once dirname(__DIR__) . '/config/config.php';
include(VIEWS_PATH . '/components/cabecera.php');
require_once MODELS_PATH . '/conex.php';
require_once MODELS_PATH . '/class_espacioEStacionamiento.php';
require_once MODELS_PATH . '/LogObserver.php';
require_once MODELS_PATH . '/EstadisticasObserver.php';

// Instanciar el gestor de espacios de estacionamiento con observers
$espacioManager = new EspacioEstacionamiento($conexion);
$espacioManager->agregarObserver(new LogObserver());
$espacioManager->agregarObserver(new EstadisticasObserver());

// Verificar si se presionó el botón de salida
if (isset($_GET['exit_id'])) {
    $vehiculo_id = intval($_GET['exit_id']);

    // Verificar si ya se le dio salida al vehículo
    $query_check_exit = "SELECT * FROM INFO1170_HistorialRegistros WHERE idVehiculo = ? AND accion = 'Salida' ORDER BY fecha DESC LIMIT 1";
    $stmt_check_exit = $conexion->prepare($query_check_exit);
    $stmt_check_exit->bind_param("i", $vehiculo_id);
    $stmt_check_exit->execute();
    $result_check_exit = $stmt_check_exit->get_result();

    if ($result_check_exit->num_rows > 0) {
        // Si ya hay un registro de salida, mostrar mensaje
        echo "<p style='color:red;'>Este vehículo ya tiene una salida registrada.</p>";
    } else {
        // Si no hay salida registrada, proceder con el registro de la salida
        // Obtener el espacio de estacionamiento del vehículo
        $query_select = "SELECT espacio_estacionamiento FROM INFO1170_VehiculosRegistrados WHERE id = ?";
        $stmt_select = $conexion->prepare($query_select);
        $stmt_select->bind_param("i", $vehiculo_id);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();

        if ($result_select->num_rows > 0) {
            $row = $result_select->fetch_assoc();
            $parking_space = $row['espacio_estacionamiento'];

            // Registrar la salida en el historial
            $query_historial = "INSERT INTO INFO1170_HistorialRegistros (idVehiculo, fecha, accion) VALUES (?, NOW(), 'Salida')";
            $stmt_historial = $conexion->prepare($query_historial);
            $stmt_historial->bind_param("i", $vehiculo_id);            if ($stmt_historial->execute()) {
                // Usar el gestor de espacios para liberar el espacio
                if ($espacioManager->liberarEspacio($parking_space)) {
                    echo "<p style='color:green;'>Salida registrada correctamente y espacio actualizado.</p>";
                } else {
                    echo "<p style='color:red;'>Error al actualizar el estado del espacio.</p>";
                }
            } else {
                echo "<p style='color:red;'>Error al registrar la salida en el historial.</p>";
            }

            $stmt_historial->close();
        } else {
            echo "<p style='color:red;'>Vehículo no encontrado.</p>";
        }

        $stmt_select->close();
    }

    $stmt_check_exit->close();
}
echo "<script>
    setTimeout(function() {
        window.location.href = '" . BASE_URL . "/estructura/views/ver_registros_vehiculos.php';
    }, 2000);
</script>";

// Mensaje adicional
echo "<p>Redirigiendo a la página de registros en 2 segundos...</p>";

include(VIEWS_PATH . '/components/pie.php');
?>
