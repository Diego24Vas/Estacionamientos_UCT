<?php
require_once dirname(__DIR__) . '/interfaces/EspacioObserver.php';

// Observer para actualizar estadísticas (ejemplo simple)
class EstadisticasObserver implements EspacioObserver {
    public function actualizar($idEspacio, $nuevoEstado) {
        // Aquí podrías actualizar una tabla de estadísticas, enviar una notificación, etc.
        // Ejemplo: solo imprime (en producción, actualizaría la base de datos o caché)
        error_log("[Estadisticas] Espacio $idEspacio ahora está $nuevoEstado");
    }
}
