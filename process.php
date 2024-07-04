<?php
if (isset($_GET['generate'])) {
    $template = $_GET['generate'];
    $row = $_GET['row'];
    $column = $_GET['column'];

    if ($template == '5160-music') {
        header("Location: generate_5160.php?row=$row&column=$column");
    } elseif ($template == '5160-uniform') {
        header("Location: generate_5160_uniform.php?row=$row&column=$column");
    } elseif ($template == '5160-equipment') {
        header("Location: generate_5160_equipment.php?row=$row&column=$column");
    } elseif ($template == '5163') {
        header("Location: generate_5163.php?row=$row&column=$column");
    } elseif ($template == '5167') {
        header("Location: generate_5167.php?row=$row&column=$column");
    }
    exit;
}
?>
