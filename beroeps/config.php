<?php

$servername = "localhost";
$username = "100228_DB";
$password = "caroorta496";
$database = "100228_DB";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}
