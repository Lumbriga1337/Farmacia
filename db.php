<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "farmacia";

// mysqli
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro mysqli: " . $conn->connect_error);
}

// PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro PDO: " . $e->getMessage());
}
