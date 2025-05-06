<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="./css/estilo_inicio.css">
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
        <form method="POST" id="loginForm"> <!-- Eliminé el atributo action -->
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
              <a href="../estructura/recuperar_contraseña.php">¿Olvidaste tu contraseña?</a>
            </label>
          </div>
          <button id="loginBtn" type="submit">Iniciar Sesión</button>
          <div class="register">
            <p>¿No tienes una cuenta? <a href="../estructura/registro.php">Regístrate por aquí</a></p>
          </div>
        </form>
      </div>
    </div>
  </section>
  <script src="../estructura/js/Inicio-Register.js"></script>
</body>
</html>
