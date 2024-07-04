<?php
require('fpdf.php');

// Database connection
$mysqli = new mysqli("127.0.0.1", "root", "", "music_library");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

class PDF extends FPDF
{
    private $column;
    private $row;

    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, '5160 Music Label', 0, 1, 'C');
        $this->Ln(10);
        $this->column = 0;
        $this->row = 0;
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function AddBarcode($data)
    {
        $blockWidth = 65; // Width of each block
        $blockHeight = 35; // Height of each block
        $x = 10 + ($this->column * $blockWidth);
        $y = 30 + ($this->row * $blockHeight);
        // $leftMargin = 10; // Left margin
        // $x = $leftMargin + ($this->column * $blockWidth);
        // $y = 30 + ($this->row * $blockHeight);

        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell($blockWidth, 5, "School name: " . $data['composer'], 0, 'L');

        $this->SetXY($x, $this->GetY());
        $this->MultiCell($blockWidth, 3, "Title: " . $data['title'], 0, 'L');

        $this->SetXY($x, $this->GetY());
        $this->SetFont('Free3of9', '', 36);
        // $this->Cell($blockWidth, 15, '*' . $data['id'] . '*', 0, 1, 'L');
        $barcodeData = str_repeat('*', 3) . $data['id'] . str_repeat('*', 3); // Add extra asterisks for width
        $this->Cell($blockWidth, 15, $barcodeData, 0, 1, 'L'); // Center align

        $this->SetXY($x, $this->GetY());
        $this->SetFont('Arial', '', 10);
        $this->MultiCell($blockWidth, -3, "ID: " . $data['id'] . "                       " . $data['library_id1'] . "" . $data['library_id2'], 0, 'L');

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


    // public function WrapText($text, $barcodeData)
    // {
    //     $this->SetFont('Free3of9', '', 36);
    //     $barcodeWidth = $this->GetStringWidth($barcodeData);
    //     $this->SetFont('Arial', '', 10);

    //     $words = explode(' ', $text);
    //     $wrappedText = '';
    //     $line = '';

    //     foreach ($words as $word) {
    //         if ($this->GetStringWidth($line . ' ' . $word) <= $barcodeWidth) {
    //             $line .= ' ' . $word;
    //         } else {
    //             $wrappedText .= trim($line) . "\n";
    //             $line = $word;
    //         }
    //     }
    //     $wrappedText .= trim($line);

    //     return $wrappedText;
    // }

}

if (isset($_GET['generate'])) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->AddFont('Free3of9', '', 'FREE3OF9.php');

    // Fetch data from the database
    $result = $mysqli->query("SELECT * FROM music");

    while ($row = $result->fetch_assoc()) {
        $pdf->AddBarcode($row);
    }

    $pdf->Output();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Barcodes</title>
</head>
<body>
    <h1>Generate Barcodes</h1>
    <form method="get">
        <button type="submit" name="generate" value="5160">Generate 5160 Barcodes</button>
        <button type="submit" name="generate" value="5163">Generate 5163 Barcodes</button>
        <button type="submit" name="generate" value="5167">Generate 5167 Barcodes</button>
    </form>
</body>
</html>
