<?php
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/login.css">
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>
  <div class="login-container">
    <div id="img-logo">
      <img src="<?php echo BASE_URL; ?>/estructura/img/logo.png" alt="logo">
    </div>
    <form method="POST" id="recoveryForm">
      <h2 style="margin-bottom: 20px;">Recuperar Contraseña</h2>
      
      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
      </div>
      
      <button type="submit" class="btn">Recuperar Contraseña</button>
      
      <div style="text-align: center; margin-top: 20px;">
        <p style="color: #ccc; font-size: 14px;">¿Ya tienes una cuenta? 
          <a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php" style="color: #0d6efd; text-decoration: none;">Inicia Sesión aquí</a>
        </p>
      </div>
    </form>
  </div>
  
  <!-- JavaScript inline para evitar problemas de cache -->
  <script>
document.addEventListener('DOMContentLoaded', function () {
    const recoveryForm = document.getElementById('recoveryForm');
    
    if (recoveryForm) {
        recoveryForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(recoveryForm);

            console.log('🔍 Iniciando recuperación con URL:', '<?php echo BASE_URL; ?>/estructura/controllers/enviar_recuperacion.php');

            fetch('<?php echo BASE_URL; ?>/estructura/controllers/enviar_recuperacion.php', {
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
                    recoveryForm.reset();
                } else {
                    alert(data.message || 'Error al procesar la solicitud');
                }
            })
            .catch(error => {
                console.error('❌ Error en recuperación:', error);
                alert('Hubo un problema al procesar la solicitud. Por favor, intente nuevamente.');
            });
        });
    }
});
</script>
</body>
</html>
