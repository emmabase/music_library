<?php
require('fpdf.php');
require('./db_connections/db_connection.php'); // Include the database connection file

class PDF5163 extends FPDF
{
    private $column = 0;
    private $row = 0;
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
        $blockWidth = 102; // Width of each block (4 inches in points)
        $blockHeight = 45.8; // Height of each block (2 inches in points)
        $rowSpacing = 10; // Extra space between rows
        $leftMargin = 10; // Left margin
        $topMargin = 20; // Top margin

        $x = $leftMargin + ($this->column * $blockWidth);
        $y = $topMargin + ($this->row * ($blockHeight + $rowSpacing));

        // $barcodeData = str_repeat('*', 5) . $data['id'] . str_repeat('*', 5);
        if ($this->generateAll || ($this->column == $this->targetColumn && $this->row == $this->targetRow)) {

            $this->SetXY($x, $y);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell($blockWidth, 5, "[School name]", 0, 'L');

            $this->SetXY($x, $this->GetY());
            $this->MultiCell($blockWidth, 5, $data['title'], 0, 'L');

            $this->SetXY($x, $this->GetY());
            $this->MultiCell($blockWidth, 6, $data['composer'] . "/" . $data['arranger'], 0, 'L');

            // Draw the barcode
            $this->SetXY($x, $this->GetY());
            $this->SetFont('LibreBarcode39-Regular', '', 30); // Barcode font size
            $this->Cell($blockWidth, 8, "*" . $data['id'], 0, 1, 'L'); // Center align

            $this->SetXY($x, $this->GetY());
            $this->SetFont('Arial', '', 10);
            $this->MultiCell($blockWidth, 4, " " . $data['id'] . "  " . $data['library_id1'] . "" . $data['library_id2'], 0, 'L');
        }
        $this->column++;
        if ($this->column == 2) {
            $this->column = 0;
            $this->row++;
        }

        if ($this->row == 5) {
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

$pdf = new PDF5163($row, $column);
$pdf->AddPage();
$pdf->AddFont('LibreBarcode39-Regular', '', 'LibreBarcode39-Regular.php');

// Fetch data from the database
$result = $mysqli->query("SELECT * FROM music");

while ($row = $result->fetch_assoc()) {
    $pdf->AddBarcode($row);
}

$pdf->Output();
