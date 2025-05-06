<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . '/fpdf186/fpdf.php';
require('conex.php');

// Obtener parámetros de filtro
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
$valor = isset($_GET['valor']) ? $_GET['valor'] : null;

// Construir la consulta SQL con filtro
$query = "SELECT nombre, apellido, patente, espacio_estacionamiento 
          FROM INFO1170_VehiculosRegistrados";

if ($filtro && $valor) {
    $query .= " WHERE $filtro LIKE ?";
}

$stmt = $conexion->prepare($query);

if ($filtro && $valor) {
    $valor = "%$valor%"; // Usar comodines para búsqueda parcial
    $stmt->bind_param("s", $valor);
}

$stmt->execute();
$result = $stmt->get_result();

// Clase personalizada para encabezado y pie de página
class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../img/images.png', 10, 8, 30);
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Universidad Catolica de Temuco', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, 'San Juan Pablo II', 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Registro de Vehiculos', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $fecha = date('d/m/Y');
        $this->Cell(0, 10, 'Fecha de descarga: ' . $fecha, 0, 0, 'C');
        $this->Ln(5);
        $this->SetY(-15);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 220, 255);

$header = ['Nombre', 'Apellido', 'Patente', 'Espacio Est.'];
$widths = [35, 35, 35, 35]; // Ajusta los anchos si es necesario
$totalWidth = array_sum($widths); // Calcula el ancho total de la tabla
$pageWidth = $pdf->GetPageWidth(); // Ancho de la página
$xOffset = ($pageWidth - $totalWidth) / 2; // Calcula el margen izquierdo

$pdf->SetX($xOffset);
foreach ($header as $i => $col) {
    $pdf->Cell($widths[$i], 10, $col, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(245, 245, 245);
$fill = false;

while ($row = $result->fetch_assoc()) {
    $pdf->SetX($xOffset);
    $pdf->Cell($widths[0], 8, $row['nombre'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[1], 8, $row['apellido'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[2], 8, $row['patente'], 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 8, $row['espacio_estacionamiento'], 1, 0, 'C', $fill);
    $pdf->Ln();
    $fill = !$fill;
}

$pdf->Output('D', 'Registro de vehiculos.pdf');
?>