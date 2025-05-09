<?php
// Incluir el archivo de FPDF
require_once('../fpdf186/fpdf.php');  // Asegúrate de que la ruta sea correcta

class PDFGen extends FPDF {

    // Constructor para inicializar la clase FPDF
    function __construct($orientation='P', $unit='mm', $size='A4') {
        parent::__construct($orientation, $unit, $size);
    }

    // Método para crear el encabezado del reporte
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Reporte de Estacionamientos', 0, 1, 'C');
        $this->Ln(10);  // Salto de línea
    }

    // Método para crear el pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página '.$this->PageNo(), 0, 0, 'C');
    }

    // Método para agregar contenido al reporte
    function AddContent($content) {
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $content);
    }

    // Método para generar el PDF
    function generatePDF($title, $content) {
        $this->AddPage();
        $this->SetTitle($title);
        $this->AddContent($content);
        $this->Output();
    }
}
?>
