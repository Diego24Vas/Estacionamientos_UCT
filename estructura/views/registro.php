<?php
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Estacionamiento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/estructura/views/css/estilo_inicio.css">
    <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js' type="module"></script>
    <script src='https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js' type="module"></script>
</head>
<body>    
    <main class="container">
        <section class="form-section">
            <div class="form-box">
                <div class="form-value">
                    <form method="POST" action="" id="registerForm">
                        <h2>Regí­strate</h2>
                        
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
                            <input type="text" name="nombre" required 
                                   pattern="[A-Za-z0-9_]{3,20}" 
                                   title="El nombre de usuario debe tener entre 3 y 20 caracteres alfanuméricos">
                            <label for="nombre">Nombre Usuario</label>
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
                            <p>¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>/estructura/views/inicio.php">Inicia Sesion Aqui</a></p>
                        </div>
                    </form>
                </div>
            </div>
    </section>
    </main>

    <?php include VIEWS_PATH . '/components/pie.php'; ?>
    
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
