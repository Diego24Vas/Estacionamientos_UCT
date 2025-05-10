
<?php
require_once('PDFGen.php');
require_once('conex.php');

class EstadisticasEstacionamiento {

    private $conexion;

    // Constructor para recibir la conexión a la base de datos
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Método para generar el reporte de estadísticas
    public function generarEstadisticas() {
        // Crear una instancia de PDFGen
        $pdf = new PDFGen();

        // Establecer el título del reporte
        $title = "Estadísticas de Uso del Estacionamiento";

        // Consultas SQL para obtener las estadísticas
        $totalReservasQuery = "SELECT COUNT(*) AS total_reservas FROM reservas";
        $reservasOcupadasQuery = "SELECT COUNT(DISTINCT espacio) AS espacios_ocupados FROM reservas WHERE fecha_reserva <= NOW()";

        // Ejecutar las consultas
        $totalReservasResult = $this->conexion->query($totalReservasQuery);
        $reservasOcupadasResult = $this->conexion->query($reservasOcupadasQuery);

        // Obtener los resultados
        $totalReservas = $totalReservasResult->fetch_assoc()['total_reservas'];
        $espaciosOcupados = $reservasOcupadasResult->fetch_assoc()['espacios_ocupados'];

        // Crear el contenido del reporte
        $content = "Total de Reservas: $totalReservas\n";
        $content .= "Espacios Ocupados: $espaciosOcupados\n";
        $content .= "------------------------------------------------------------\n";
        $content .= "Detalles adicionales podrían incluir el porcentaje de uso del estacionamiento.";

        // Llamar al método de la clase PDFGen para generar el PDF
        $pdf->generatePDF($title, $content);
    }
}
?>
