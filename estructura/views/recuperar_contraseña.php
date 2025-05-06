<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña</title>
  <link rel="stylesheet" href="../estructura/css/estilo_inicio.css"> <!-- Usamos el mismo archivo CSS -->
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>
  <header>
    <img src="../estructura/img/logo.png" alt="IUCT Logo">
  </header>
  <section>
    <div class="form-box">
      <div class="form-value">
        <form method="POST" id="recoveryForm"> <!-- Formulario de recuperación -->
          <h2>Recuperar Contraseña</h2>
          <div class="inputbox">
              <ion-icon name="mail-outline"></ion-icon>
              <input type="email" name="email" required>
              <label for="">Correo electrónico</label>
          </div>
          <button type="submit">Recuperar Contraseña</button>
          <div class="register">
            <p>¿Ya tienes una cuenta? <a href="../estructura/inicio.php">Inicia Sesión aquí</a></p>
          </div>
        </form>
      </div>
    </div>
  </section>
  <script src="../estructura/js/Inicio-Register.js"></script> <!-- Validación común -->
</body>
</html>
