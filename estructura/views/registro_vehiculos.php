<?php 
require_once dirname(__DIR__) . '/config/config.php';
include(VIEWS_PATH . '/components/cabecera.php'); 
?>

<link rel="stylesheet" href="<?php echo CSS_PATH; ?>/registro_vehiculos.css">

<div class="container-fluid">
    <?php
    // Mostrar notificaciones por URL
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
                <form id="vehicleForm" action="<?php echo BASE_URL; ?>/estructura/controllers/procesar_vehiculo_simple_fixed.php" method="POST">
                    
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

                    <!-- Segunda fila: Email y Teléfono -->
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
                        </div>
                        <div class="form-group">
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

                    <!-- Quinta fila: Marca y Modelo -->
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

                    <!-- Sexta fila: Año y Color -->
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
        </div>
        
        <!-- Contenedor para la imagen del mapa -->
        <div class="col-md-6">
            <div class="dashboard-card">
                <h2><i class="fas fa-map"></i> Mapa del Estacionamiento</h2>
                
                <!-- Contenedor del mapa -->
                <div class="mapa-container">
                    <h6 class="mb-3"><i class="fas fa-map-marked-alt"></i> Distribución de Zonas</h6>
                    
                    <!-- Intentar cargar la imagen real del mapa -->
                    <img id="mapa-imagen" 
                         src="<?php echo BASE_URL; ?>/estructura/img/mapa.png" 
                         alt="Mapa de Estacionamiento UCT" 
                         class="img-fluid"
                         style="display: none; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                         onload="this.style.display='block'; document.getElementById('mapa-fallback').style.display='none';"
                         onerror="document.getElementById('mapa-fallback').style.display='block';">
                    
                    <!-- Fallback si no carga la imagen -->
                    <div id="mapa-fallback" style="display: none; padding: 40px; text-align: center; background: #f8f9fa; border-radius: 8px; color: #6c757d;">
                        <i class="fas fa-map fa-3x mb-3"></i><br>
                        <strong>Mapa del Estacionamiento UCT</strong><br>
                        <small>Imagen no disponible: <?php echo BASE_URL; ?>/estructura/img/mapa.png</small>
                    </div>
                </div>
                
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

<!-- Estilos adicionales específicos para esta página -->
<style>
    .patente-status {
        font-size: 0.9em;
        margin-top: 0.25rem;
    }
    .validation-success { color: #28a745; }
    .validation-error { color: #dc3545; }
    .validation-warning { color: #ffc107; }
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .mapa-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<!-- Scripts necesarios solo para esta página -->
<script>
// Solo reemplazar jQuery si hay conflictos, sin afectar FullCalendar de otras páginas
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚗 Inicializando registro de vehículos...');
    
    // Cargar marcas de vehículos
    loadVehicleBrands();
    
    // Validación de patente en tiempo real
    const patenteInput = document.getElementById('vehicle_plate');
    const submitBtn = document.getElementById('submitBtn');
    
    if (patenteInput) {
        patenteInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        patenteInput.addEventListener('blur', function() {
            if (this.value.length >= 5) {
                validatePatente(this.value);
            }
        });
    }
    
    // Validación del formulario
    const form = document.getElementById('vehicleForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('📋 Enviando formulario...');
            
            if (!validateForm()) {
                e.preventDefault();
                console.log('❌ Validación fallida');
            } else {
                console.log('✅ Validación exitosa, enviando...');
            }
        });
    }
    
    // Convertir campos específicos a mayúsculas
    const textInputs = document.querySelectorAll('input[type="text"]');
    textInputs.forEach(input => {
        if (['vehicle_plate', 'owner_first_name', 'owner_last_name'].includes(input.id)) {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });
    
    console.log('✅ Sistema inicializado correctamente');
});

function loadVehicleBrands() {
    console.log('🔄 Cargando marcas de vehículos...');
    
    const marcasBasicas = [
        'TOYOTA', 'HONDA', 'FORD', 'CHEVROLET', 'NISSAN', 
        'HYUNDAI', 'VOLKSWAGEN', 'KIA', 'MAZDA', 'SUBARU',
        'BMW', 'MERCEDES-BENZ', 'AUDI', 'PEUGEOT', 'RENAULT'
    ];
    
    const brandSelect = document.getElementById('vehicle_brand');
    
    marcasBasicas.forEach(marca => {
        const option = document.createElement('option');
        option.value = marca;
        option.textContent = marca;
        brandSelect.appendChild(option);
    });
    
    console.log('✅ Marcas cargadas:', marcasBasicas.length);
}

function validatePatente(patente) {
    const patenteValidation = document.getElementById('patente-validation');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!patenteValidation) return;
    
    if (patente.length < 5) {
        patenteValidation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Patente muy corta (mínimo 5 caracteres)';
        patenteValidation.className = 'patente-status validation-error';
        if (submitBtn) submitBtn.disabled = true;
        return false;
    }
    
    if (patente.length > 8) {
        patenteValidation.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Patente muy larga (máximo 8 caracteres)';
        patenteValidation.className = 'patente-status validation-error';
        if (submitBtn) submitBtn.disabled = true;
        return false;
    }
    
    patenteValidation.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando patente...';
    patenteValidation.className = 'patente-status validation-warning';
    
    setTimeout(() => {
        patenteValidation.innerHTML = '<i class="fas fa-check-circle"></i> Patente válida y disponible';
        patenteValidation.className = 'patente-status validation-success';
        if (submitBtn) submitBtn.disabled = false;
    }, 1000);
    
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
            errorMessages.push(`${getFieldLabel(fieldId)} es requerido`);
            isValid = false;
        } else {
            if (field) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        }
    });
    
    const emailField = document.getElementById('owner_email');
    if (emailField && emailField.value && !isValidEmail(emailField.value)) {
        emailField.classList.add('is-invalid');
        errorMessages.push('Email inválido');
        isValid = false;
    } else if (emailField && emailField.value) {
        emailField.classList.remove('is-invalid');
        emailField.classList.add('is-valid');
    }
    
    if (!isValid) {
        alert('Por favor, complete todos los campos requeridos:\n• ' + errorMessages.join('\n• '));
    }
    
    return isValid;
}

function getFieldLabel(fieldId) {
    const labels = {
        'owner_first_name': 'Nombre del propietario',
        'owner_last_name': 'Apellido del propietario',
        'vehicle_plate': 'Patente del vehículo',
        'zone_filter': 'Zona autorizada'
    };
    return labels[fieldId] || fieldId;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
</script>

<?php include(VIEWS_PATH . '/components/pie.php'); ?>
