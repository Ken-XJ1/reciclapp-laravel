<?php
$servername = "127.0.0.1"; 
$username   = "root";
$password   = "10kenneth10";
$dbname     = "reciclapp_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
