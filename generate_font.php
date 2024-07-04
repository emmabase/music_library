<?php
require('fpdf.php');
require('makefont/makefont.php');

// Generate the font files
MakeFont('font/LibreBarcode39-Regular.ttf', 'cp1252');
?>
