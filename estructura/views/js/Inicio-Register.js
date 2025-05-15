document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.getElementById('loginBtn');
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');

    // Manejo del formulario de registro de usuarios
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(registerForm);

            fetch(`${BASE_URL}/controllers/registrar_user.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    registerForm.reset();
                    window.location.href = `${BASE_URL}/estructura/views/inicio.php`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al registrar el usuario.');
            });
        });
    }

    // Manejo del formulario de inicio de sesión
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(loginForm);

            fetch(`${BASE_URL}/controllers/procesar_inicio.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    window.location.href = `${BASE_URL}/estructura/views/pag_inicio.php`;
                } else {
                    alert(data.message || 'Usuario o contraseña incorrectos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al iniciar sesión.');
            });
        });
    }
});
