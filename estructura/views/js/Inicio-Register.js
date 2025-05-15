document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.getElementById('loginBtn');
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(registerForm);

            fetch('../controllers/registrar_user.php', {
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
                    window.location.href = 'inicio.php'; // ruta relativa también
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

            fetch('/~rpedraza/Estacionamientos_UCT/estructura/controllers/procesar_inicio.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                if (data.status === "success") {
                    window.location.href = '/~rpedraza/Estacionamientos_UCT/estructura/views/pag_inicio.php';
                } else {
                    alert(data.message || 'Usuario o contraseña incorrectos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al iniciar sesión. Por favor, intente nuevamente.');
            });
        });
    }
});
