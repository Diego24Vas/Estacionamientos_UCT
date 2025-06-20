<?php 
require_once dirname(__DIR__) . '/config/config.php';

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
