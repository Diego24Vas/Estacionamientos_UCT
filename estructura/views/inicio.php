<?php 
require_once dirname(__DIR__) . '/config/config.php';

?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/estilo_inicio.css">
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>
  <header>
    <img src="<?php echo BASE_URL; ?>/estructura/img/logo.png" alt="IUCT Logo">
  </header>
  <section>
    <div class="form-box">
      <div class="form-value">
        <form method="POST" id="loginForm">
          <h2>Inicio Sesión</h2>
          <div class="inputbox">
            <ion-icon name="mail-outline"></ion-icon>
            <input type="text" name="username" required>
            <label for="">Nombre Usuario</label>
          </div>
          <div class="inputbox">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="password" required>
            <label for="">Contraseña</label>
          </div>
          <div class="forget">
            <label>
              <input type="checkbox"> Recuérdame
            </label>
            <label>
            <a href="<?php echo BASE_URL; ?>/estructura/views/recuperar_contraseña.php">¿Olvidaste tu contraseña?</a>
            </label>
          </div>
          <button id="loginBtn" type="submit">Iniciar Sesión</button>
          <div class="register">
          <a href="<?php echo BASE_URL; ?>/estructura/views/registro.php">Regístrate por aquí</a></p>
          </div>
        </form>
      </div>    </div>
  </section>
  
  <!-- JavaScript inline para evitar problemas de cache -->
  <script>
document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.getElementById('loginBtn');
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(registerForm);

            fetch('<?php echo BASE_URL; ?>/estructura/controllers/registrar_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.status === "success") {
                    alert(data.message);
                    registerForm.reset();
                    window.location.href = '<?php echo BASE_URL; ?>/estructura/views/inicio.php';
                } else {
                    alert(data.message || 'Error al registrar usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al registrar el usuario. Por favor, intente nuevamente.');
            });
        });
    }

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
