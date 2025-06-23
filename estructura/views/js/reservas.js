document.addEventListener('DOMContentLoaded', function () {
    cargarEventos();
    verificarProximosEventos();
    loadParkingStats(); // Cargar estadísticas de estacionamiento
    
    // Agregar validación de patente en tiempo real
    const patenteInput = document.getElementById('patente');
    const patenteValidation = document.getElementById('patente-validation');
    const submitBtn = document.getElementById('submitBtn');
    
    if (patenteInput) {
        patenteInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        patenteInput.addEventListener('blur', function() {
            validarPatente(this.value, 'patente-validation', submitBtn);
        });
    }
    
    // Validación de patente en modal de edición
    const editarPatenteInput = document.getElementById('editarPatente');
    if (editarPatenteInput) {
        editarPatenteInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        editarPatenteInput.addEventListener('blur', function() {
            validarPatente(this.value, 'editarPatente-validation');
        });
    }
    
    // Validar disponibilidad cuando cambian los campos relevantes
    const formFields = ['zona', 'fecha', 'hora_inicio', 'hora_fin'];
    formFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', checkAvailabilityOnChange);
        }
    });
    
    // Validar formulario antes de enviar
    const reservaForm = document.getElementById('reservaForm');
    if (reservaForm) {
        reservaForm.addEventListener('submit', function(e) {
            const horaInicio = document.getElementById('hora_inicio').value;
            const horaFin = document.getElementById('hora_fin').value;
            
            if (horaInicio && horaFin && horaFin <= horaInicio) {
                e.preventDefault();
                alert('La hora de fin debe ser posterior a la hora de inicio');
                return false;
            }
            
            // Verificar disponibilidad final antes de enviar
            e.preventDefault();
            checkAvailabilityBeforeSubmit();
        });
    }
});

function validarPatente(patente, elementoId, submitBtn = null) {
    const patenteValidation = document.getElementById(elementoId);
    
    if (!patente || patente.trim() === '') {
        patenteValidation.innerHTML = '';
        if (submitBtn) submitBtn.disabled = true;
        return;
    }
    
    // Mostrar indicador de carga
    patenteValidation.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Verificando...</span>';
    
    fetch(`../services/validar_patente.php?patente=${encodeURIComponent(patente)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                patenteValidation.innerHTML = '<span class="validation-error"><i class="fas fa-exclamation-triangle"></i> Error al validar patente</span>';
                if (submitBtn) submitBtn.disabled = true;
            } else if (data.existe) {
                patenteValidation.innerHTML = '<span class="validation-success"><i class="fas fa-check-circle"></i> Patente registrada en el sistema</span>';
                if (submitBtn) submitBtn.disabled = false;
            } else {
                patenteValidation.innerHTML = '<span class="validation-error"><i class="fas fa-times-circle"></i> Patente no encontrada. Debe registrar el vehículo primero.</span>';
                if (submitBtn) submitBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error al validar patente:', error);
            patenteValidation.innerHTML = '<span class="validation-warning"><i class="fas fa-exclamation-triangle"></i> Error al validar patente</span>';
            if (submitBtn) submitBtn.disabled = true;
        });
}

function cargarEventos() {
    fetch('../services/obtener_eventos.php')
        .then(response => response.json())
        .then(data => {
            const recordatoriosDiv = document.getElementById('recordatorios');
            recordatoriosDiv.innerHTML = '';

            if (data.length > 0) {
                data.forEach(evento => {
                    const eventoDiv = document.createElement('div');
                    eventoDiv.className = 'card-reserva border border-primary rounded p-3 mb-3';
                    
                    // Determinar el color de la zona
                    const colorZona = {
                        'A': 'primary',
                        'B': 'success', 
                        'C': 'warning',
                        'D': 'info'
                    }[evento.zona] || 'secondary';
                    
                    eventoDiv.innerHTML = `
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-2">
                                    <i class="fas fa-calendar-alt text-primary"></i> 
                                    <strong>${evento.evento}</strong>
                                    <span class="badge bg-${colorZona} ms-2">Zona ${evento.zona}</span>
                                </h6>
                                <div class="row text-muted small">
                                    <div class="col-md-6">
                                        <i class="fas fa-calendar"></i> ${evento.fecha}<br>
                                        <i class="fas fa-clock"></i> ${evento.hora_inicio} - ${evento.hora_fin}
                                    </div>
                                    <div class="col-md-6">
                                        <i class="fas fa-user"></i> ${evento.usuario}<br>
                                        <i class="fas fa-car"></i> ${evento.patente}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-actions">
                                    <button class="btn btn-warning btn-sm" onclick="editarReserva(${evento.id})" title="Editar reserva">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarReserva(${evento.id})" title="Eliminar reserva">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    recordatoriosDiv.appendChild(eventoDiv);
                });
            } else {
                recordatoriosDiv.innerHTML = `
                    <div class="text-center text-muted p-4">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p class="mb-0">No hay reservas activas actualmente.</p>
                        <small>Las nuevas reservas aparecerán aquí.</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar los eventos reservados:', error);
            document.getElementById('recordatorios').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error al cargar las reservas. Por favor, recargue la página.
                </div>
            `;
        });
}

function editarReserva(id) {
    fetch(`../services/obtener_reserva.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data) {                document.getElementById('editarReservaId').value = data.id || '';
                document.getElementById('editarEvento').value = data.evento || '';
                document.getElementById('editarFecha').value = data.fecha || '';
                document.getElementById('editarHoraInicio').value = data.hora_inicio || '';
                document.getElementById('editarHoraFin').value = data.hora_fin || '';
                document.getElementById('editarUsuario').value = data.usuario || '';
                document.getElementById('editarPatente').value = data.patente || '';
                document.getElementById('editarZona').value = data.zona || '';

                const modal = new bootstrap.Modal(document.getElementById('editarReservaModal'));
                modal.show();
            } else {
                alert("Error al cargar los datos de la reserva.");
            }
        })
        .catch(error => console.error('Error al obtener los datos de la reserva:', error));
}

function eliminarReserva(id) {
    if (!id || isNaN(id)) {
        alert('ID de reserva inválido.');
        return;
    }

    // Usar SweetAlert2 si está disponible, sino usar confirm nativo
    const confirmar = confirm('¿Está seguro de que desea eliminar esta reserva?\n\nEsta acción no se puede deshacer.');
    
    if (confirmar) {
        // Mostrar indicador de carga
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
        btn.disabled = true;
        
        fetch(`../controllers/eliminar_reservas.php?id=${id}`, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show';
                    alertDiv.innerHTML = `
                        <i class="fas fa-check-circle"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.container').prepend(alertDiv);
                    
                    // Recargar la lista de eventos
                    cargarEventos();
                    
                    // Auto-ocultar alerta después de 5 segundos
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    }, 5000);
                } else {
                    alert('Error: ' + (data.message || 'Error desconocido al eliminar la reserva.'));
                }
            })
            .catch(error => {
                console.error('Error al eliminar la reserva:', error);
                alert('Error de conexión al eliminar la reserva.');
            })
            .finally(() => {
                // Restaurar botón
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    }
}

document.getElementById('editarReservaForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const id = document.getElementById('editarReservaId').value;
    const evento = document.getElementById('editarEvento').value;
    const fecha = document.getElementById('editarFecha').value;
    const horaInicio = document.getElementById('editarHoraInicio').value;
    const horaFin = document.getElementById('editarHoraFin').value;
    const usuario = document.getElementById('editarUsuario').value;
    const patente = document.getElementById('editarPatente').value;
    const zona = document.getElementById('editarZona').value;

    // Validación de horarios
    if (horaFin <= horaInicio) {
        alert('La hora de fin debe ser posterior a la hora de inicio');
        return;
    }

    // Mostrar indicador de carga
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitBtn.disabled = true;

    fetch('../controllers/procesar_edicion_reserva.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            id, 
            evento, 
            fecha, 
            horaInicio, 
            horaFin, 
            usuario,
            patente,
            zona
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Mostrar mensaje de éxito
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').prepend(alertDiv);
            
            // Recargar eventos y cerrar modal
            cargarEventos();
            const modal = bootstrap.Modal.getInstance(document.getElementById('editarReservaModal'));
            modal.hide();
            
            // Auto-ocultar alerta después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error al actualizar la reserva:', error);
        alert('Error de conexión al actualizar la reserva.');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// NUEVO: Verificar eventos próximos
function verificarProximosEventos() {
    fetch('../controllers/notificar_eventos.php')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(evento => {
                    alertarEventoProximo(evento);
                });
            }
        })
        .catch(error => console.error('Error al verificar eventos próximos:', error));
}

function alertarEventoProximo(evento) {
    const alertaDiv = document.createElement('div');
    alertaDiv.className = 'alert alert-warning alert-dismissible fade show';
    alertaDiv.innerHTML = `
        <strong>¡Evento Próximo!</strong><br>
        <strong>Evento:</strong> ${evento.evento}<br>
        <strong>Fecha:</strong> ${evento.fecha}<br>
        <strong>Hora:</strong> ${evento.hora_inicio}<br>
        <strong>Zona:</strong> ${evento.zona}<br>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    const container = document.querySelector('.container');
    container.prepend(alertaDiv); // Inserta la alerta al inicio del contenedor
}

// Función para cargar las zonas disponibles (comentada porque usamos zonas estáticas)
/*
function cargarZonas() {
    fetch('../services/get_parking_spaces.php')
        .then(response => response.json())
        .then(data => {
            const zonaSelect = document.getElementById('zona');
            zonaSelect.innerHTML = '<option value="">Seleccione una zona</option>';
            
            data.forEach(zona => {
                const option = document.createElement('option');
                option.value = zona.id;
                option.textContent = `${zona.nombre} (${zona.tipos_vehiculos_permitidos})`;
                zonaSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al cargar las zonas:', error));
}
*/

// === NUEVAS FUNCIONES PARA CONTROL DE CUPOS ===

/**
 * Verificar disponibilidad cuando cambian los campos del formulario
 */
function checkAvailabilityOnChange() {
    const zona = document.getElementById('zona').value;
    const fecha = document.getElementById('fecha').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (zona && fecha && horaInicio && horaFin) {
        checkParkingAvailability(zona, fecha, horaInicio, horaFin);
    }
}

/**
 * Verificar disponibilidad antes de enviar el formulario
 */
function checkAvailabilityBeforeSubmit() {
    const zona = document.getElementById('zona').value;
    const fecha = document.getElementById('fecha').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (!zona || !fecha || !horaInicio || !horaFin) {
        showNotification('error', 'Todos los campos son requeridos');
        return;
    }
    
    fetch(`${window.appConfig.paths.controllers}/parking_api.php?action=availability&zona=${zona}&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                // Mostrar información de disponibilidad y proceder
                showAvailabilityInfo(data);
                document.getElementById('reservaForm').submit();
            } else {
                // Bloquear envío y mostrar error
                showNotification('error', `No hay espacios disponibles en la zona ${zona}. Espacios ocupados: ${data.occupiedSpaces}/${data.maxSpaces}`);
            }
        })
        .catch(error => {
            console.error('Error verificando disponibilidad:', error);
            showNotification('warning', 'No se pudo verificar disponibilidad. Procediendo con la reserva...');
            document.getElementById('reservaForm').submit();
        });
}

/**
 * Verificar disponibilidad de espacios de estacionamiento
 */
function checkParkingAvailability(zona, fecha, horaInicio, horaFin) {
    const availabilityIndicator = document.getElementById('availability-indicator') || createAvailabilityIndicator();
    
    availabilityIndicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando disponibilidad...';
    availabilityIndicator.className = 'availability-indicator checking';
    
    fetch(`${window.appConfig.paths.controllers}/parking_api.php?action=availability&zona=${zona}&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
        .then(response => response.json())
        .then(data => {
            updateAvailabilityIndicator(data, zona);
        })
        .catch(error => {
            console.error('Error verificando disponibilidad:', error);
            availabilityIndicator.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error verificando disponibilidad';
            availabilityIndicator.className = 'availability-indicator error';
        });
}

/**
 * Crear indicador de disponibilidad
 */
function createAvailabilityIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'availability-indicator';
    indicator.className = 'availability-indicator mt-2';
    
    // Insertar después del campo zona
    const zonaField = document.getElementById('zona').parentNode;
    zonaField.appendChild(indicator);
    
    return indicator;
}

/**
 * Actualizar indicador de disponibilidad
 */
function updateAvailabilityIndicator(data, zona) {
    const indicator = document.getElementById('availability-indicator');
    const submitBtn = document.getElementById('submitBtn');
    
    if (data.available) {
        indicator.innerHTML = `
            <div class="alert alert-success p-2 mb-0">
                <i class="fas fa-check-circle"></i> 
                Espacios disponibles: ${data.availableSpaces}/${data.maxSpaces} 
                <small>(${data.utilizationPercentage}% ocupado)</small>
            </div>
        `;
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Crear Reserva';
        submitBtn.className = 'btn btn-primary w-100';
        
        if (data.utilizationPercentage >= 80) {
            showNotification('warning', `Zona ${zona} está al ${data.utilizationPercentage}% de capacidad`);
        }
    } else {
        indicator.innerHTML = `
            <div class="alert alert-danger p-2 mb-0">
                <i class="fas fa-times-circle"></i> 
                No hay espacios disponibles (${data.occupiedSpaces}/${data.maxSpaces})
            </div>
        `;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-ban"></i> Zona Completa';
        submitBtn.className = 'btn btn-secondary w-100';
        
        showNotification('error', `Zona ${zona} está completa. Seleccione otra zona u horario.`);
    }
}

/**
 * Mostrar información de disponibilidad
 */
function showAvailabilityInfo(data) {
    const message = `Reserva confirmada. Espacios restantes: ${data.availableSpaces}/${data.maxSpaces}`;
    
    if (data.utilizationPercentage >= 90) {
        showNotification('warning', message + ` (${data.utilizationPercentage}% ocupado)`);
    } else {
        showNotification('success', message);
    }
}

/**
 * Cargar estadísticas de estacionamiento
 */
function loadParkingStats() {
    fetch(`${window.appConfig.paths.controllers}/parking_api.php?action=stats`)
        .then(response => response.json())
        .then(data => {
            updateParkingStatsDisplay(data);
        })
        .catch(error => {
            console.error('Error cargando estadísticas:', error);
        });
}

/**
 * Actualizar display de estadísticas
 */
function updateParkingStatsDisplay(stats) {
    // Crear o actualizar el display de estadísticas
    let statsContainer = document.getElementById('parking-stats');
    if (!statsContainer) {
        statsContainer = createStatsContainer();
    }
    
    let statsHtml = '<h6><i class="fas fa-chart-bar"></i> Estado Actual de Estacionamientos</h6>';
    statsHtml += '<div class="row">';
    
    Object.values(stats).forEach(zoneStat => {
        const statusClass = getStatusClass(zoneStat.status);
        const progressWidth = zoneStat.utilizationPercentage;
        
        statsHtml += `
            <div class="col-md-6 mb-2">
                <div class="card border-${statusClass} h-100">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">Zona ${zoneStat.zona}</h6>
                        <div class="progress mb-1" style="height: 10px;">
                            <div class="progress-bar bg-${statusClass}" style="width: ${progressWidth}%"></div>
                        </div>
                        <small class="text-muted">
                            ${zoneStat.occupiedSpaces}/${zoneStat.maxSpaces} ocupados (${progressWidth}%)
                        </small>
                    </div>
                </div>
            </div>
        `;
    });
    
    statsHtml += '</div>';
    statsContainer.innerHTML = statsHtml;
}

/**
 * Crear contenedor de estadísticas
 */
function createStatsContainer() {
    const container = document.createElement('div');
    container.id = 'parking-stats';
    container.className = 'parking-stats mt-3 p-3 bg-light rounded';
    
    // Insertar en la sección de instrucciones
    const instructionsCard = document.querySelector('.col-md-6:last-child .card-body');
    if (instructionsCard) {
        instructionsCard.appendChild(container);
    }
    
    return container;
}

/**
 * Obtener clase CSS para el estado
 */
function getStatusClass(status) {
    switch (status) {
        case 'available': return 'success';
        case 'busy': return 'info';
        case 'warning': return 'warning';
        case 'critical': return 'danger';
        default: return 'secondary';
    }
}

/**
 * Liberar espacio de estacionamiento
 */
function releaseSpace(reservationId) {
    if (!confirm('¿Está seguro de que desea liberar este espacio?')) {
        return;
    }
    
    fetch(`${window.appConfig.paths.controllers}/parking_api.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'release',
            reservationId: reservationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            cargarEventos(); // Recargar eventos
            loadParkingStats(); // Actualizar estadísticas
        } else {
            showNotification('error', data.message || 'Error al liberar espacio');
        }
    })
    .catch(error => {
        console.error('Error liberando espacio:', error);
        showNotification('error', 'Error de conexión al liberar espacio');
    });
}
