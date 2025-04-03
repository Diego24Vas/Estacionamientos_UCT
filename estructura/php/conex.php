<?php
class Conexion {
    private $host = "db.inf.uct.cl"; 
    private $usuario = "dvasquez";   
    private $contrasena = "aVOeGU27CWrnwXMyx";    
    private $baseDeDatos = "A2024_dvasquez"; 

    private $conexion;

    public function __construct() {
        $this->conexion = new mysqli($this->host, $this->usuario, $this->contrasena, $this->baseDeDatos);
        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    public function prepare($sql) {
        return $this->conexion->prepare($sql);
    }

    public function __destruct() {
        $this->conexion->close();
    }
}
?>
