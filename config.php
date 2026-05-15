<?php
$host     = "127.0.0.1";
$dbname   = "hotel";
$username = "root";
$password = "";
$port     = 3307;          // ← Ajouter le port 3307 !

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>