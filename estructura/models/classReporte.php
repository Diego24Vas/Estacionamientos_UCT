<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once CONFIG_PATH . '/conex.php';

class Reporte {
    // Propiedades
    private $titulo;
    private $contenido;
    private $fechaCreacion;
    private $conexion;

    // Constructor
    public function __construct($conexion, $titulo, $contenido) {
        $this->conexion = $conexion;
        $this->titulo = $titulo;
        $this->contenido = $contenido;
        $this->fechaCreacion = date('Y-m-d H:i:s'); // Fecha de creación actual
    }

    //  Seguire aqui, solo que devo ver otras cosas con los casos de uso


}

?>