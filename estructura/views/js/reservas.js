document.addEventListener('DOMContentLoaded', function () {
    cargarEventos();
    verificarProximosEventos();
    
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
