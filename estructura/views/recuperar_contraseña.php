<?php
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña</title>
  <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/estilo_inicio.css">
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
        <form method="POST" id="recoveryForm">
          <h2>Recuperar Contraseña</h2>
          <div class="inputbox">
              <ion-icon name="mail-outline"></ion-icon>
              <input type="email" name="email" required>
              <label for="">Correo electrónico</label>
          </div>
          <button type="submit">Recuperar Contraseña</button>
          <div class="register">
            <p>¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php">Inicia Sesión aquí</a></p>
          </div>
        </form>
      </div>    </div>
  </section>
  
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
