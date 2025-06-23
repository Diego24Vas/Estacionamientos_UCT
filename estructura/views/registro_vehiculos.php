<?php 
// Aplicar Dependency Injection
require_once dirname(__DIR__) . '/core/Application.php';

// Inicializar DI
$app = Application::getInstance();
$notificationService = $app->get('service.notification');
$viewHelper = $app->get('service.view');

// Configuración tradicional para compatibilidad
require_once dirname(__DIR__) . '/config/config.php';
include(VIEWS_PATH . '/components/cabecera.php'); 
?>
<link rel="stylesheet" href="<?php echo $viewHelper->url('css/registro_vehiculos.css'); ?>">

<div class="container-fluid">
    <?php
    // Mostrar notificaciones usando DI
    echo $notificationService->renderNotifications();
    
    // Compatibilidad con mensajes por URL
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-check-circle"></i> Vehículo registrado exitosamente';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_GET['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    ?>
    
    <div class="row">
        <!-- Contenedor para el formulario -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h2><i class="fas fa-car"></i> Registro de Vehículos</h2>
                <form id="vehicleForm" action="<?php echo $viewHelper->url('controllers/procesar_vehiculo_simple_fixed.php'); ?>" method="POST">
                    <?php echo $viewHelper->csrfField(); ?>
                    
                    <!-- Primera fila: Nombre y Apellido -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="owner_first_name"><i class="fas fa-user"></i> Nombre del propietario:</label>
                            <input type="text" id="owner_first_name" name="owner_first_name" class="form-control" required>
                            <div class="invalid-feedback">El nombre es requerido</div>
                        </div>
                        <div class="form-group">
                            <label for="owner_last_name"><i class="fas fa-user"></i> Apellido del propietario:</label>
                            <input type="text" id="owner_last_name" name="owner_last_name" class="form-control" required>
                            <div class="invalid-feedback">El apellido es requerido</div>
                        </div>
                    </div>

                    <!-- Segunda fila: Email y Teléfono (opcionales) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="owner_email"><i class="fas fa-envelope"></i> Email (opcional):</label>
                            <input type="email" id="owner_email" name="owner_email" class="form-control">
                            <div class="invalid-feedback">Ingrese un email válido</div>
                        </div>
                        <div class="form-group">
                            <label for="owner_phone"><i class="fas fa-phone"></i> Teléfono (opcional):</label>
                            <input type="tel" id="owner_phone" name="owner_phone" class="form-control">
                        </div>
                    </div>

                    <!-- Tercera fila: Patente y Zona -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicle_plate"><i class="fas fa-id-card"></i> Patente del Vehículo:</label>
                            <input type="text" id="vehicle_plate" name="vehicle_plate" class="form-control" required maxlength="8" style="text-transform: uppercase;">
                            <div id="patente-validation" class="patente-status"></div>
                            <div class="invalid-feedback">La patente es requerida</div>
                        </div>                        <div class="form-group">
                            <label for="zone_filter"><i class="fas fa-map-marker-alt"></i> Zona autorizada:</label>
                            <select id="zone_filter" name="zone_filter" class="form-control" required>
                                <option value="">Seleccione una zona</option>
                                <option value="A">Zona A - Administrativa</option>
                                <option value="B">Zona B - Académica</option>
                                <option value="C">Zona C - Deportiva</option>
                                <option value="D">Zona D - Visitantes</option>
                            </select>
                            <div class="invalid-feedback">Seleccione una zona</div>
                        </div>
                    </div>

                    <!-- Cuarta fila: Tipo de vehículo y Usuario -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicle_type"><i class="fas fa-car-side"></i> Tipo de vehículo:</label>
                            <select id="vehicle_type" name="vehicle_type" class="form-control">
                                <option value="Auto">Auto</option>
                                <option value="Camioneta">Camioneta</option>
                                <option value="Motocicleta">Motocicleta</option>
                                <option value="Bicicleta">Bicicleta</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_type"><i class="fas fa-user-tag"></i> Tipo de usuario:</label>
                            <select id="user_type" name="user_type" class="form-control">
                                <option value="Regular">Regular</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Docente">Docente</option>
                                <option value="Administrativo">Administrativo</option>
                                <option value="Visitante">Visitante</option>
                            </select>
                        </div>
                    </div>

                    <!-- Quinta fila: Marca y Modelo (opcionales) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicle_brand"><i class="fas fa-industry"></i> Marca (opcional):</label>
                            <select id="vehicle_brand" name="vehicle_brand" class="form-control select-search">
                                <option value="">Seleccione una marca</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vehicle_model"><i class="fas fa-car"></i> Modelo (opcional):</label>
                            <input type="text" id="vehicle_model" name="vehicle_model" class="form-control">
                        </div>
                    </div>

                    <!-- Sexta fila: Año y Color (opcionales) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicle_year"><i class="fas fa-calendar-alt"></i> Año (opcional):</label>
                            <input type="number" id="vehicle_year" name="vehicle_year" class="form-control" min="1990" max="<?php echo date('Y') + 1; ?>">
                        </div>
                        <div class="form-group">
                            <label for="vehicle_color"><i class="fas fa-palette"></i> Color (opcional):</label>
                            <input type="text" id="vehicle_color" name="vehicle_color" class="form-control">
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <div class="form-group text-center">
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Vehículo
                        </button>
                    </div>
                </form>
            </div>
        </div>        <!-- Contenedor para la imagen del mapa -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h2><i class="fas fa-map"></i> Mapa del Estacionamiento</h2>
                <img src="<?php echo $viewHelper->url('img/mapa.png'); ?>" alt="Mapa de Estacionamiento" class="img-fluid">
                
                <!-- Información de zonas -->
                <div class="mt-3">
                    <h6><i class="fas fa-info-circle"></i> Información de Zonas</h6>
                    <ul class="list-unstyled">
                        <li><strong>Zona A:</strong> Administrativa (Funcionarios)</li>
                        <li><strong>Zona B:</strong> Académica (Docentes)</li>
                        <li><strong>Zona C:</strong> Deportiva (Eventos)</li>
                        <li><strong>Zona D:</strong> Visitantes (Público general)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .patente-status {
        font-size: 0.9em;
        margin-top: 0.25rem;
    }
    .validation-success { color: #28a745; }
    .validation-error { color: #dc3545; }
    .validation-warning { color: #ffc107; }
    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .form-group {
        flex: 1;
    }
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cargar marcas dinámicamente
    loadVehicleBrands();
    
    // Validación de patente en tiempo real
    const patenteInput = document.getElementById('vehicle_plate');
    const submitBtn = document.getElementById('submitBtn');
    
    if (patenteInput) {
        patenteInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        patenteInput.addEventListener('blur', function() {
            validatePatente(this.value);
        });
    }
      // Validación del formulario
    const form = document.getElementById('vehicleForm');
    form.addEventListener('submit', function(e) {
        // Debug: Mostrar datos del formulario
        const formData = new FormData(this);
        const formObject = {};
        for (let [key, value] of formData) {
            formObject[key] = value;
        }
        console.log('Datos del formulario:', formObject);
        
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    
    // Habilitar Select2 para búsqueda de marcas
    $('#vehicle_brand').select2({
        placeholder: 'Buscar marca',
        allowClear: true
    });
});

function loadVehicleBrands() {
    const brandSelect = document.getElementById('vehicle_brand');
    
    fetch('<?php echo $viewHelper->url('services/get_vehicle_brands.php'); ?>')
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
        .catch(error => {
            console.error('Error al cargar marcas:', error);
            // Agregar marcas básicas como fallback
            const marcasBasicas = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan', 'Hyundai', 'Volkswagen'];
            marcasBasicas.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca;
                option.textContent = marca;
                brandSelect.appendChild(option);
            });
        });
}

function validatePatente(patente) {
    const patenteValidation = document.getElementById('patente-validation');
    const submitBtn = document.getElementById('submitBtn');
    
    if (patente.length < 5) {
        patenteValidation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Patente muy corta (mínimo 5 caracteres)';
        patenteValidation.className = 'patente-status validation-error';
        return false;
    }
    
    if (patente.length > 8) {
        patenteValidation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Patente muy larga (máximo 8 caracteres)';
        patenteValidation.className = 'patente-status validation-error';
        return false;
    }
    
    // Verificar si la patente ya existe
    fetch(`<?php echo $viewHelper->url('services/validar_patente.php'); ?>?patente=${patente}`)
        .then(response => response.json())
        .then(data => {
            if (data.valida) {
                patenteValidation.innerHTML = '<i class="fas fa-times-circle"></i> Esta patente ya está registrada';
                patenteValidation.className = 'patente-status validation-error';
                submitBtn.disabled = true;
            } else {
                patenteValidation.innerHTML = '<i class="fas fa-check-circle"></i> Patente disponible';
                patenteValidation.className = 'patente-status validation-success';
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error validando patente:', error);
            patenteValidation.innerHTML = '<i class="fas fa-question-circle"></i> No se pudo validar la patente';
            patenteValidation.className = 'patente-status validation-warning';
        });
    
    return true;
}

function validateForm() {
    const requiredFields = ['owner_first_name', 'owner_last_name', 'vehicle_plate', 'zone_filter'];
    let isValid = true;
    let errorMessages = [];
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const value = field ? field.value.trim() : '';
        
        if (!value) {
            if (field) {
                field.classList.add('is-invalid');
            }
            errorMessages.push(`El campo ${fieldId} es requerido`);
            isValid = false;
        } else {
            if (field) {
                field.classList.remove('is-invalid');
            }
        }
    });
    
    // Validar email si se proporciona
    const emailField = document.getElementById('owner_email');
    if (emailField && emailField.value && !isValidEmail(emailField.value)) {
        emailField.classList.add('is-invalid');
        errorMessages.push('Email inválido');
        isValid = false;
    } else if (emailField) {
        emailField.classList.remove('is-invalid');
    }
    
    // Mostrar errores si hay
    if (!isValid) {
        console.log('Errores de validación:', errorMessages);
        alert('Por favor, complete todos los campos requeridos:\n- ' + errorMessages.join('\n- '));
    }
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper para mostrar notificaciones
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
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alert, container.firstChild.nextSibling);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
};
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


<?php include(VIEWS_PATH . '/components/pie.php'); ?>
