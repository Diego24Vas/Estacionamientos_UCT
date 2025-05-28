<?php
require_once 'classAdmin.php';

class AdministradorFactory {
    public static function crearAdministradorGeneral($id, $nombre, $email, $password, $conexion) {
        $privilegios = 'general';
        return new Administrador($id, $nombre, $email, $password, $conexion, $privilegios);
    }

    public static function crearAdministradorEspacio($id, $nombre, $email, $password, $conexion) {
        $privilegios = 'espacio';
        return new Administrador($id, $nombre, $email, $password, $conexion, $privilegios);
    }
}

?>