<?php
require_once dirname(__DIR__) . '/interfaces/EspacioObserver.php';

// Observer para registrar logs de cambios de estado
global $logfile;
$logfile = __DIR__ . '/../logs/espacio_estacionamiento.log';

class LogObserver implements EspacioObserver {
    public function actualizar($idEspacio, $nuevoEstado) {
        global $logfile;
        $mensaje = date('Y-m-d H:i:s') . " - Espacio $idEspacio cambiado a $nuevoEstado\n";
        file_put_contents($logfile, $mensaje, FILE_APPEND);
    }
}
