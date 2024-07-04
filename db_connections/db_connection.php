<?php
// Database connection
$mysqli = new mysqli("127.0.0.1", "root", "", "music_library");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}