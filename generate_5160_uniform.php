<?php
require('fpdf.php');
require('./db_connections/db_connection.php'); // Include the database connection file


class PDF5160Uniform extends FPDF
{
    private $column;
    private $row;
    private $targetColumn;
    private $targetRow;
    private $generateAll;

    public function __construct($targetRow = null, $targetColumn = null)
    {
        parent::__construct();
        $this->targetRow = isset($targetRow) ? $targetRow - 1 : null; // Adjust for 0-indexing
        $this->targetColumn = isset($targetColumn) ? $targetColumn - 1 : null; // Adjust for 0-indexing
        $this->generateAll = is_null($targetRow) || is_null($targetColumn);
    }

    public function AddBarcode($data)
    {
        // In the context of FPDF, the unit of measurement used for dimensions such as $blockWidth can be specified when you create the PDF object. The default unit is millimeters (mm), but you can also use points (pt), centimeters (cm), or inches (in).

        // Here’s a quick overview:

        // 1 point = 1/72 inch
        // 1 inch = 25.4 millimeters
        // 1 centimeter = 10 millimeters
        // If you want to specify the unit as pixels, you need to use points, because there is a direct relationship between points and pixels: 1 point is typically considered to be 1 pixel at 72 DPI (dots per inch).

        // When you create the FPDF object, you can specify the unit of measurement:
        // $pdf = new FPDF('P', 'pt', 'A4'); // Here 'pt' stands for points

        $blockWidth = 67.6; // Width of each block (2.625 inches in points)
        $blockHeight = 25.5; // Height of each block (1 inch in points)
        $rowSpacing = 10; // Extra space between rows
        $leftMargin = 10; // Left margin
        $topMargin = 10; // Top margin


        $x = $leftMargin + ($this->column * $blockWidth);
        $y = $topMargin + ($this->row * ($blockHeight + $rowSpacing));

        $barcodeValue = $data['barcode'] ? $data['barcode'] : $data['inventorynumber'];

        if ($this->generateAll || ($this->column == $this->targetColumn && $this->row == $this->targetRow)) {
            $this->SetXY($x, $y);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell($blockWidth, 5, "[School name]", 0, 'L');

            $this->SetXY($x, $this->GetY());
            $this->MultiCell($blockWidth, 3, $data['brand'], 0, 'L');

            $this->SetXY($x, $this->GetY());
            $this->MultiCell($blockWidth, 3, $data['uniformtype'] . " " . $data['itemnumber'], 0, 'L');

            // Draw the barcode
            $this->SetXY($x, $this->GetY());
            $this->SetFont('LibreBarcode39-Regular', '', 30); // Barcode font size
            $this->Cell($blockWidth, 10, "*" . $barcodeValue . "*", 0, 1, 'L'); // Left align barcode

            $this->SetXY($x, $this->GetY());
            $this->SetFont('Arial', '', 10);
            $this->MultiCell($blockWidth, 3, " " . $barcodeValue, 0, 'L');
        }

        $this->column++;
        if ($this->column == 3) {
            $this->column = 0;
            $this->row++;
        }

        if ($this->row == 10) {
            $this->AddPage();
            $this->row = 0;
        }
    }
}

if (isset($_GET['row']) && isset($_GET['column']) && $_GET['row'] !== "" && $_GET['column'] !== "") {
    $row = intval($_GET['row']);
    $column = intval($_GET['column']);
} else {
    $row = null;
    $column = null;
}

$pdf = new PDF5160Uniform($row, $column);
$pdf->AddPage();
$pdf->AddFont('LibreBarcode39-Regular', '', 'LibreBarcode39-Regular.php');

// Fetch data from the database
$result = $mysqli->query("SELECT * FROM uniform");

while ($row = $result->fetch_assoc()) {
    $pdf->AddBarcode($row);
}

$pdf->Output();
