## Implementación de CSRF en Formularios

Este documento explica cómo implementar protección CSRF en formularios HTML y solicitudes AJAX para el sistema de estacionamientos.

### 1. Formulario de Login

```html
<form id="login-form" method="post">
    <div class="form-group">
        <label for="username">Usuario</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <!-- El token CSRF se insertará aquí -->
    <div id="csrf-container"></div>
    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
</form>

<script>
// Obtener token CSRF antes de enviar el formulario
document.addEventListener('DOMContentLoaded', function() {
    fetch('estructura/controllers/obtener_csrf_token.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Insertar el campo oculto con el token CSRF
                document.getElementById('csrf-container').innerHTML = data.csrf_field;
            }
        })
        .catch(error => console.error('Error obteniendo token CSRF:', error));
    
    // Manejar envío del formulario
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('estructura/controllers/procesar_inicio.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = 'panel.php'; // Redirigir a panel de usuario
            } else {
                alert(data.message); // Mostrar mensaje de error
            }
        })
        .catch(error => console.error('Error en login:', error));
    });
});
</script>
```

### 2. Formulario de Registro

```html
<form id="registro-form" method="post">
    <div class="form-group">
        <label for="nombre">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="reg-password">Contraseña</label>
        <input type="password" class="form-control" id="reg-password" name="password" required>
    </div>
    <!-- El token CSRF se insertará aquí -->
    <div id="reg-csrf-container"></div>
    <button type="submit" class="btn btn-primary">Registrarse</button>
</form>

<script>
// Obtener token CSRF antes de enviar el formulario
document.addEventListener('DOMContentLoaded', function() {
    fetch('estructura/controllers/obtener_csrf_token.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Insertar el campo oculto con el token CSRF
                document.getElementById('reg-csrf-container').innerHTML = data.csrf_field;
            }
        })
        .catch(error => console.error('Error obteniendo token CSRF:', error));
    
    // Manejar envío del formulario
    document.getElementById('registro-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('estructura/controllers/registrar_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Registro exitoso. Por favor inicia sesión.');
                window.location.href = 'login.php'; // Redirigir a login
            } else {
                alert(data.message); // Mostrar mensaje de error
            }
        })
        .catch(error => console.error('Error en registro:', error));
    });
});
</script>
```

### 3. Botón de Logout

```html
<button id="logout-btn" class="btn btn-danger">Cerrar Sesión</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar autenticación y obtener token CSRF
    fetch('estructura/controllers/verificar_autenticacion.php')
        .then(response => response.json())
        .then(data => {
            if (data.authenticated) {
                // Guardar el token CSRF para usar en logout
                localStorage.setItem('csrfToken', data.csrf_token);
            } else {
                // Redirigir a login si no está autenticado
                window.location.href = 'login.php';
            }
        });
    
    // Manejar cierre de sesión
    document.getElementById('logout-btn').addEventListener('click', function() {
        // Obtener token CSRF guardado
        const csrfToken = localStorage.getItem('csrfToken');
        
        if (!csrfToken) {
            alert('Error de seguridad: No se encontró token CSRF');
            return;
        }
        
        // URL con el token CSRF
        const logoutUrl = `estructura/controllers/logout.php?csrf_token=${csrfToken}`;
        
        fetch(logoutUrl)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Limpiar token guardado
                    localStorage.removeItem('csrfToken');
                    window.location.href = 'login.php'; // Redirigir a login
                } else {
                    alert(data.message); // Mostrar mensaje de error
                }
            })
            .catch(error => console.error('Error en logout:', error));
    });
});
</script>
```

### 4. Validación en cada página protegida

```html
<script>
// Verificar autenticación en cada carga de página
document.addEventListener('DOMContentLoaded', function() {
    fetch('estructura/controllers/verificar_autenticacion.php')
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                // Redirigir a login si no está autenticado
                window.location.href = 'login.php';
            } else {
                // Usuario autenticado, mostrar contenido
                document.getElementById('user-name').textContent = data.user.nombre;
                
                // Guardar token CSRF para otras operaciones
                localStorage.setItem('csrfToken', data.csrf_token);
            }
        })
        .catch(error => {
            console.error('Error verificando autenticación:', error);
            window.location.href = 'login.php';
        });
});
</script>
```

### 5. AJAX con protección CSRF

```javascript
// Función para realizar peticiones AJAX con token CSRF
function fetchConCSRF(url, options = {}) {
    // Obtener token CSRF actual
    return fetch('estructura/controllers/obtener_csrf_token.php')
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error('Error obteniendo token CSRF');
            }
            
            // Configurar opciones con el token CSRF
            const csrfToken = data.csrf_token;
            
            // Si hay FormData, añadir el token
            if (options.body instanceof FormData) {
                options.body.append('csrf_token', csrfToken);
            } 
            // Si no hay body, crear uno nuevo
            else if (!options.body) {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                options.body = formData;
                
                if (!options.method) {
                    options.method = 'POST';
                }
            }
            // Si hay body como objeto, añadir token
            else if (typeof options.body === 'object') {
                options.body.csrf_token = csrfToken;
            }
            
            // Realizar petición con token CSRF
            return fetch(url, options);
        });
}

// Ejemplo de uso
fetchConCSRF('estructura/controllers/alguna_accion.php', {
    method: 'POST',
    body: new FormData(document.getElementById('mi-formulario'))
})
.then(response => response.json())
.then(data => {
    // Procesar respuesta
    console.log(data);
})
.catch(error => console.error('Error:', error));
```

Esta guía muestra cómo implementar la protección CSRF en los formularios de login, registro y logout, así como en otras páginas protegidas del sistema de estacionamientos.
