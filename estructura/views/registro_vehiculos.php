<?php 
require_once dirname(__DIR__) . '/config/config.php';
include(VIEWS_PATH . '/components/cabecera.php'); 
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/registro_vehiculos.css">

<div class="container-fluid">
    <div class="row">
        <!-- Contenedor para el formulario -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h2>Registro de Vehículos</h2>
                <form action="registro_vehiculos.php" method="POST">
                    <!-- Primera fila: Nombre y Apellido -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="owner_first_name">Nombre del propietario:</label>
                            <input type="text" id="owner_first_name" name="owner_first_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="owner_last_name">Apellido del propietario:</label>
                            <input type="text" id="owner_last_name" name="owner_last_name" class="form-control" required>
                        </div>
                    </div>

                    <!-- Tercera fila: Tipo de Usuario y Patente -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicle_plate">Patente del Vehículo:</label>
                            <input type="text" id="vehicle_plate" name="vehicle_plate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="zone_filter">Selecciona la zona:</label>
                            <select id="zone_filter" name="zone_filter" class="form-control" required>
                                <option value="">Selecciona una zona</option>
                                <option value="Zona A">Zona A</option>
                                <option value="Zona B">Zona B</option>
                                <option value="Zona C">Zona C</option>
                                <option value="Zona D">Zona D</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campo oculto para espacio de estacionamiento -->
                    <input type="hidden" id="parking_space" name="parking_space" required>

                    <!-- Botón de envío -->
                    <div class="form-group text-center">
                        <input type="submit" value="Registrar Vehículo" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>

        <!-- Contenedor para la imagen del mapa -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h2>Mapa del Estacionamiento</h2>
                <img src="../img/mapa.png" alt="Mapa de Estacionamiento" class="img-fluid">
            </div>
        </div>
    </div>
</div>



<script>
// Cargar marcas dinámicamente al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    const brandSelect = document.getElementById('vehicle_brand');

    // Petición AJAX para obtener las marcas de vehículos
    fetch('get_vehicle_brands.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            data.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand.name;
                option.textContent = brand.name;
                brandSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar marcas:', error));
});

// Habilitar búsqueda dentro del select
document.addEventListener('DOMContentLoaded', function () {
    const selectSearch = document.querySelector('.select-search');
    $(selectSearch).select2({
        placeholder: 'Busca una marca',
        allowClear: true
    });
});
</script>

<script>
// JavaScript para filtrar los espacios de estacionamiento según la zona seleccionada
document.getElementById('zone_filter').addEventListener('change', function() {
    var zone = this.value;
    var parkingSpaceSelect = document.getElementById('parking_space');
    
    // Limpiar opciones previas
    parkingSpaceSelect.innerHTML = '<option value="">Selecciona un espacio</option>';

    if (zone) {
        // Realizar una petición AJAX para obtener los espacios de la zona seleccionada
        fetch('get_parking_spaces.php?zone=' + zone)
            .then(response => response.json())
            .then(data => {
                // Agregar las opciones de los espacios disponibles a la lista desplegable
                data.forEach(space => {
                    var option = document.createElement('option');
                    option.value = space.IdEstacionamiento;
                    option.textContent = space.IdEstacionamiento;
                    parkingSpaceSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al obtener los espacios:', error));
    }
});
</script>

<script>
// Convertir a mayúsculas solo los campos de texto
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input[type="text"], input[type="number"]');

    inputs.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
});
</script>


<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once MODELS_PATH . '/class_espacioEStacionamiento.php';
require_once MODELS_PATH . '/LogObserver.php';
require_once MODELS_PATH . '/EstadisticasObserver.php';
require_once CONFIG_PATH . '/conex.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear instancia de EspacioEstacionamiento con observers
    $espacioEstacionamiento = new EspacioEstacionamiento($conexion);
    $logObserver = new LogObserver();
    $estadisticasObserver = new EstadisticasObserver($conexion);
    
    $espacioEstacionamiento->agregarObserver($logObserver);
    $espacioEstacionamiento->agregarObserver($estadisticasObserver);

    // Obtener los datos del formulario
    $owner_first_name = $_POST['owner_first_name'] ?? '';
    $owner_last_name = $_POST['owner_last_name'] ?? '';
    $vehicle_plate = $_POST['vehicle_plate'] ?? '';
    $parking_space = $_POST['parking_space'] ?? '';

    // Validar los datos antes de insertar
    if (empty($owner_first_name) || empty($owner_last_name) || empty($vehicle_plate) || empty($parking_space)) {
        die("<p style='color:red;'>Error: Todos los campos son obligatorios.</p>");
    }

    // Consulta para insertar datos del vehículo
    $query_insertar = "INSERT INTO INFO1170_VehiculosRegistrados 
        (nombre, apellido, patente, espacio_estacionamiento) 
        VALUES (?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($query_insertar);
    $stmt->bind_param("ssss", $owner_first_name, $owner_last_name, $vehicle_plate, $parking_space);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Vehículo registrado exitosamente.</p>";

        // Obtener el ID del vehículo insertado
        $vehiculo_id = $conexion->insert_id;

        // Insertar en el historial
        $query_historial = "INSERT INTO INFO1170_HistorialRegistros (idVehiculo, fecha, accion) 
        VALUES (?, NOW(), 'Entrada')";
        $stmt_historial = $conexion->prepare($query_historial);
        $stmt_historial->bind_param("i", $vehiculo_id);

        if (!$stmt_historial->execute()) {
            die("<p style='color:red;'>Error al insertar en el historial: " . $stmt_historial->error . "</p>");
        }

        // Usar el método de la clase para ocupar el espacio (esto disparará las notificaciones del Observer)
        if ($espacioEstacionamiento->ocuparEspacio($parking_space, $vehicle_plate)) {
            echo "<p>Espacio de estacionamiento actualizado correctamente.</p>";
        } else {
            echo "<p style='color:red;'>Error al actualizar el estado del espacio.</p>";
        }

    } else {
        echo "<p style='color:red;'>Error al registrar el vehículo: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>



<script>
// JavaScript para filtrar los espacios de estacionamiento según la zona seleccionada
document.getElementById('zone_filter').addEventListener('change', function () {
    var zone = this.value;

    // Realizar una petición AJAX para obtener los espacios de la zona seleccionada
    fetch('get_parking_spaces.php?zone=' + zone)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error); // Mostrar error si no hay espacios disponibles
                document.getElementById('parking_space').value = ''; // Limpia cualquier valor previo
            } else {
                // Selección automática del primer espacio disponible
                const firstAvailableSpace = data[0]; // Primer espacio disponible
                document.getElementById('parking_space').value = firstAvailableSpace.IdEstacionamiento;

                // Opcional: Informar al usuario
                alert(`Espacio asignado automáticamente: ${firstAvailableSpace.IdEstacionamiento}`);
            }
        })
        .catch(error => console.error('Error al obtener los espacios:', error));
});

</script>
<script>
// Convertir a mayúsculas solo los campos de texto
document.addEventListener('DOMContentLoaded', function () {
    // Selecciona solo los campos de texto (input[type="text"] y input[type="number"])
    const inputs = document.querySelectorAll('input[type="text"], input[type="number"]');

    inputs.forEach(input => {
        input.addEventListener('input', function () {
            // Convierte el valor a mayúsculas
            this.value = this.value.toUpperCase();
        });
    });
});
</script>


<?php include('pie.php'); ?>