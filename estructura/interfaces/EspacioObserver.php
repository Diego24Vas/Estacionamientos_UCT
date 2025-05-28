<?php
// Interfaz para Observer de Espacio de Estacionamiento
interface EspacioObserver {
    public function actualizar($idEspacio, $nuevoEstado);
}
