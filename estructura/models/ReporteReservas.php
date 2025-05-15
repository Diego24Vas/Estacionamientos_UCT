<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/PDFGen.php';  // Actualizado para usar ruta relativa
require_once dirname(__DIR__) . '/config/conex.php';  // Actualizado para usar ruta relativa

class ReporteReservas {

    private $conexion;

    // Constructor para recibir la conexión a la base de datos
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Método para generar el reporte de reservas
    public function generarReporte() {
        // Crear una instancia de PDFGen
        $pdf = new PDFGen();

        // Establecer el título del reporte
        $title = "Reporte de Reservas de Estacionamiento";

        // Consulta SQL para obtener las reservas
        $query = "SELECT r.id, r.fecha_reserva, u.nombre AS usuario, r.vehiculo, r.espacio FROM reservas r JOIN usuarios u ON r.usuario_id = u.id ORDER BY r.fecha_reserva DESC";
        $result = $this->conexion->query($query);

        // Crear el contenido del reporte
        $content = "ID Reserva | Fecha Reserva | Usuario | Vehículo | Espacio\n";
        $content .= "-----------------------------------------------------------\n";

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $content .= "{$row['id']} | {$row['fecha_reserva']} | {$row['usuario']} | {$row['vehiculo']} | {$row['espacio']}\n";
            }
        } else {
            $content .= "No se encontraron reservas.\n";
        }

        // Llamar al método de la clase PDFGen para generar el PDF
        $pdf->generatePDF($title, $content);
    }
}


/*

ejemplo de uso

<?php
require_once('ReporteReservas.php');
require_once('EstadisticasEstacionamiento.php');

// Crear una conexión a la base de datos
$conexion = new mysqli('localhost', 'usuario', 'contraseña', 'base_de_datos');

// Crear instancias de las clases
$reporteReservas = new ReporteReservas($conexion);
$estadisticasEstacionamiento = new EstadisticasEstacionamiento($conexion);

// Generar los reportes
$reporteReservas->generarReporte();
$estadisticasEstacionamiento->generarEstadisticas();
?>


*/

?>
