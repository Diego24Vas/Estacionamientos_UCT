<?php 
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/services/session_manager.php';

// Crear carpeta de logs si no existe
$log_dir = dirname(__DIR__) . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Registrar acceso a la página de inicio
file_put_contents(
    $log_dir . '/inicio_access.log',
    date('Y-m-d H:i:s') . " - Acceso a inicio.php - IP: " . $_SERVER['REMOTE_ADDR'] . 
    " - Referrer: " . ($_SERVER['HTTP_REFERER'] ?? 'Directo') . PHP_EOL,
    FILE_APPEND
);

// Comprobar si la sesión ya está iniciada antes de intentar redirigir
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Registrar estado de sesión
file_put_contents(
    $log_dir . '/inicio_session.log',
    date('Y-m-d H:i:s') . " - Estado sesión: " . (is_authenticated() ? 'Autenticado como: '.$_SESSION['usuario'] : 'No autenticado') . PHP_EOL,
    FILE_APPEND
);

// Redirigir si ya está autenticado
if (is_authenticated()) {
    // Construir URL absoluta para evitar problemas de redirección
    $redirect_url = BASE_URL . '/estructura/views/pag_inicio.php';
    
    // Registrar redirección
    file_put_contents(
        $log_dir . '/inicio_redirect.log',
        date('Y-m-d H:i:s') . " - Redirigiendo usuario autenticado a: " . $redirect_url . PHP_EOL,
        FILE_APPEND
    );
    
    if (!headers_sent()) {
        header("Location: " . $redirect_url);
        exit();
    } else {
        echo '<script>window.location.href="' . $redirect_url . '";</script>';
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/login.css">
</head>
<body>

  <div class="login-container">
    <div id="img-logo">
      <img src="<?php echo BASE_URL; ?>/estructura/img/logo.png" alt="logo">
    </div>
    <form method="POST" id="loginForm">
      <div class="form-group">
        <label for="username">Nombre de Usuario</label>
        <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="********" required>
      </div>

      <button type="submit" class="btn">Entrar</button>

      <!-- Botones adicionales -->
      <div class="extra-buttons">
        <button type="button" class="btn-secondary" onclick="location.href='<?php echo BASE_URL; ?>/estructura/views/registro.php'">Registrarse</button>
        <button type="button" class="btn-secondary" onclick="location.href='<?php echo BASE_URL; ?>/estructura/views/recuperar_contraseña.php'">Recuperar Contraseña</button>
      </div>
    </form>
  </div>
  
  <!-- JavaScript inline para evitar problemas de cache -->
  <script>
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(loginForm);

            console.log('🔍 Iniciando login con URL:', '<?php echo BASE_URL; ?>/estructura/controllers/procesar_inicio.php');

            fetch('<?php echo BASE_URL; ?>/estructura/controllers/procesar_inicio.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('📡 Respuesta del servidor:', response);
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                console.log('📊 Datos recibidos:', data);
                if (data.status === "success") {
                    console.log('✅ Login exitoso, redirigiendo a:', '<?php echo BASE_URL; ?>/estructura/views/pag_inicio.php');
                    window.location.href = '<?php echo BASE_URL; ?>/estructura/views/pag_inicio.php';
                } else {
                    alert(data.message || 'Usuario o contraseña incorrectos');
                }
            })
            .catch(error => {
                console.error('❌ Error en login:', error);
                alert('Hubo un problema al iniciar sesión. Por favor, intente nuevamente.');
            });
        });
    }
});
</script>
</body>
</html>
