<?php
require('fpdf.php');

class PDF5167 extends FPDF
{
    private $column = 0;
    private $row = 0;
    private $startId = 12345601;

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

    function AddBarcode($id)
    {
        $blockWidth = 48; // Setting the Width of each block (1.75 inches in points)
        $blockHeight = 11.7; // Setting Height of each block (0.5 inches in points)
        $rowSpacing = 1.5; // Define Extra space between rows
        $leftMargin = 10; // Define Left margin
        $topMargin = 10; //Set Top margin

        $x = $leftMargin + ($this->column * $blockWidth);
        $y = $topMargin + ($this->row * ($blockHeight + $rowSpacing));

        // A random 7-digit number, just top populate data
        $barcodeData =  str_pad($id, 7, '0', STR_PAD_LEFT);
        if ($this->generateAll || ($this->column == $this->targetColumn && $this->row == $this->targetRow)) {


            $this->SetXY($x, $y);
            $this->SetFont('LibreBarcode39-Regular', '', 24); //Adjust the Barcode font size
            $this->Cell($blockWidth, 5, '*' . $barcodeData, 0, 1, 'C'); // Make Barcode Center align

            $this->SetXY($x, $this->GetY());
            $this->SetFont('Arial', '', 8);
            $this->Cell($blockWidth, 5, "MMO " . $barcodeData, 0, 1, 'C'); // Center align text

        }

        $this->column++;
        if ($this->column == 4) {
            $this->column = 0;
            $this->row++;
        }

        if ($this->row == 20) {
            $this->AddPage();
            $this->row = 0;
        }
    }

    function GenerateBarcodes()
    {
        for ($i = 0; $i < 80; $i++) {
            $this->AddBarcode($this->startId + $i);
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

$pdf = new PDF5167($row, $column);
$pdf->AddPage();
$pdf->AddFont('LibreBarcode39-Regular', '', 'LibreBarcode39-Regular.php');
$pdf->GenerateBarcodes();
$pdf->Output();
