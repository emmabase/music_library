<?php
require('fpdf.php');
require('./db_connections/db_connection.php'); // Include the database connection file

class PDF5160Equipment extends FPDF
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
        $blockWidth = 67.5; // Width of each block (2.625 inches in points)
        $blockHeight = 25.5; // Height of each block (1 inch in points)
        $rowSpacing = 10; // Extra space between rows
        $leftMargin = 10; // Left margin
        $topMargin = 10; // Top margin

        $x = $leftMargin + ($this->column * $blockWidth);
        $y = $topMargin + ($this->row * $blockHeight + $rowSpacing);

        $barcodeValue = $data['barcode'] ?  $data['barcode'] : $data['inventorynumber'];

        // $barcodeData = str_repeat('*', 1) . $barcodeValue; // Add extra asterisks for width
        if ($this->generateAll || ($this->column == $this->targetColumn && $this->row == $this->targetRow)) {

        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9.5);
        $this->MultiCell($blockWidth, 3, "[School name]", 0, 'L');

        $this->SetXY($x, $this->GetY());
        $this->MultiCell($blockWidth, 3,  $data['brand']. " ". $data['description'], 0, 'L');
        
        $this->SetXY($x, $this->GetY());
        $this->MultiCell($blockWidth, 5,  $data['modelnumber']. " " .$data['serialnumber'], 0, 'L');

        // Draw the barcode
        $this->SetXY($x, $this->GetY());
        $this->SetFont('LibreBarcode39-Regular', '', 36); // Barcode font size
        $this->Cell($blockWidth, 10, "*". $barcodeValue . "*", 0, 1, 'L'); // Left align

        $this->SetXY($x, $this->GetY());
        $this->SetFont('Arial', '', 9.5);
        $this->MultiCell($blockWidth, 3, " " . $barcodeValue , 0, 'L');

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

// Pass the row and column values to the PDF constructor
$pdf = new PDF5160Equipment($row, $column);
$pdf->AddPage();
$pdf->AddFont('LibreBarcode39-Regular', '', 'LibreBarcode39-Regular.php');

// Fetch data from the database
$result = $mysqli->query("SELECT * FROM equipment");

while ($row = $result->fetch_assoc()) {
    $pdf->AddBarcode($row);
}

$pdf->Output();

