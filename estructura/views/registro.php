<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once VIEWS_PATH . '/components/cabecera.php';
?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Estacionamiento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/registro.css">
    <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
    <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>
    <?php include VIEWS_PATH . '/components/cabecera.php'; ?>
    
    <main class="container">
        <section class="form-section">
            <div class="form-box">
                <div class="form-value">
                    <form method="POST" action="index.php?controller=usuario&action=registrar" id="registerForm">
                        <h2>Regístrate</h2>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="inputbox">
                            <ion-icon name="person-outline"></ion-icon>
                            <input type="text" name="username" required 
                                   pattern="[A-Za-z0-9_]{3,20}" 
                                   title="El nombre de usuario debe tener entre 3 y 20 caracteres alfanuméricos">
                            <label for="username">Nombre Usuario</label>
                        </div>

                        <div class="inputbox">
                            <ion-icon name="mail-outline"></ion-icon>
                            <input type="email" name="email" required>
                            <label for="email">Correo</label>
                        </div>

                        <div class="inputbox">
                            <ion-icon name="lock-closed-outline"></ion-icon>
                            <input type="password" name="password" required 
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                   title="La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número">
                            <label for="password">Contraseña</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Registrarse</button>
                        
                        <div class="register">
                            <p>¿Ya tienes una cuenta? <a href="index.php?controller=usuario&action=login">Inicia Sesión aquí</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include VIEWS_PATH . '/components/pie.php'; ?>
    
    <script src="<?php echo JS_PATH; ?>/Inicio-Register.js"></script>
</body>
</html>
