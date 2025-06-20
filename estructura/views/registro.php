<?php
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Estacionamiento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/login.css">
    <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
    <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>    
  <div class="login-container">
    <div id="img-logo">
      <img src="<?php echo BASE_URL; ?>/estructura/img/logo.png" alt="logo">
    </div>
    <form method="POST" id="registerForm">
      <h2 style="margin-bottom: 20px;">Registro</h2>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
          <?php 
          echo $_SESSION['error'];
          unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="nombre">Nombre de Usuario</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre de usuario" required 
               pattern="[A-Za-z0-9_]{3,20}" 
               title="El nombre de usuario debe tener entre 3 y 20 caracteres alfanuméricos">
      </div>

      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="********" required 
               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
               title="La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número">
      </div>

      <button type="submit" class="btn">Registrarse</button>
      
      <div style="text-align: center; margin-top: 20px;">
        <p style="color: #ccc; font-size: 14px;">¿Ya tienes una cuenta? 
          <a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php" style="color: #0d6efd; text-decoration: none;">Inicia Sesión Aquí</a>
        </p>
      </div>
    </form>
  </div>

  <!-- JavaScript inline para evitar problemas de cache -->
  <script>
document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(registerForm);

            console.log('🔍 Iniciando registro con URL:', '<?php echo BASE_URL; ?>/estructura/controllers/registrar_user.php');

            fetch('<?php echo BASE_URL; ?>/estructura/controllers/registrar_user.php', {
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
                    alert(data.message);
                    registerForm.reset();
                    console.log('✅ Registro exitoso, redirigiendo a:', '<?php echo BASE_URL; ?>/estructura/views/inicio.php');
                    window.location.href = '<?php echo BASE_URL; ?>/estructura/views/inicio.php';
                } else {
                    alert(data.message || 'Error al registrar usuario');
                }
            })
            .catch(error => {
                console.error('❌ Error en registro:', error);
                alert('Hubo un problema al registrar el usuario. Por favor, intente nuevamente.');
            });
        });
    }
});
</script>
</body>
</html>
