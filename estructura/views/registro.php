<?php
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/registro.css">
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
  <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>
  <header>
    <img src="<?php echo ROOT_PATH; ?>/img/logo.png" alt="IUCT Logo">
  </header>
  <section>
    <div class="form-box">
      <div class="form-value">
        <form method="POST" id="registerForm">
          <h2>Regístrate</h2>
          <div class="inputbox">
              <ion-icon name="person-outline"></ion-icon>
              <input type="text" name="username" required>
              <label for="">Nombre Usuario</label>
          </div>
          <div class="inputbox">
            <ion-icon name="mail-outline"></ion-icon>
            <input type="email" name="email" required>
            <label for="">Correo</label>
          </div>
          <div class="inputbox">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="password" required>
            <label for="">Contraseña</label>
          </div>
          <button id="registerBtn" type="submit">Registrarse</button>
          <div class="register">
            <p>¿Ya tienes una cuenta? <a href="<?php echo VIEWS_PATH; ?>/inicio.php">Inicia Sesión aquí</a></p>
          </div>
        </form>
      </div>
    </div>
  </section>
  <script src="<?php echo JS_PATH; ?>/Inicio-Register.js"></script>
</body>
</html>
