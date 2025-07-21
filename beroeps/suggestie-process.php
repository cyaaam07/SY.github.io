<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = isset($_POST['naam']) ? trim($_POST['naam']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $onderwerp = isset($_POST['onderwerp']) ? trim($_POST['onderwerp']) : '';
    $suggestie = isset($_POST['suggestie']) ? trim($_POST['suggestie']) : '';

    if (!$onderwerp || !$suggestie) {
        die("Vul het onderwerp en de suggestie in.");
    }

    try {
        $sql = "INSERT INTO suggesties (naam, email, onderwerp, suggestie) 
                VALUES (:naam, :email, :onderwerp, :suggestie)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':onderwerp', $onderwerp);
        $stmt->bindParam(':suggestie', $suggestie);
        $stmt->execute();

        header("Location: bedankt.html");
        exit;
    } catch (PDOException $e) {
        die("Er is een fout opgetreden: " . $e->getMessage());
    }
} else {
    die("Ongeldige toegang.");
}
