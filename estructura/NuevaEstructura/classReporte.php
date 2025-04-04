<?php
include('conex.php');

class Reporte {
    // Propiedades
    private $titulo;
    private $contenido;
    private $fechaCreacion;

    // Constructor
    public function __construct($titulo, $contenido) {
        $this->titulo = $titulo;
        $this->contenido = $contenido;
        $this->fechaCreacion = date('Y-m-d H:i:s'); // Fecha de creación actual
    }

    //  Seguire aqui, solo que devo ver otras cosas con los casos de uso


}

?>