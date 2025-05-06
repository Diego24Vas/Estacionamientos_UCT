document.addEventListener('DOMContentLoaded', function () {
    // Verificar ocupación máxima
    function checkMaxOccupancy() {
        fetch('../php/obtener_estado.php')
            .then(response => response.json())
            .then(data => {
                if (data.maxOccupancyReached) {
                    alert('Se ha alcanzado la ocupación máxima del estacionamiento.');
                }
            })
            .catch(error => console.error('Error al verificar la ocupación:', error));
    }

    // Enviar recordatorios para liberar espacios reservados
    function sendReleaseReminders() {
        fetch('../php/recordatorios.php')
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    console.log(data.message);
                }
            })
            .catch(error => console.error('Error al enviar recordatorios:', error));
    }

    // Llamar a las funciones al cargar la página
    checkMaxOccupancy();
    sendReleaseReminders();
});
