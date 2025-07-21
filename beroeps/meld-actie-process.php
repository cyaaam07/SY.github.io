<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

function uploadFotos($files) {
    $uploadedFiles = [];
    $uploadDir = __DIR__ . '/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    for ($i = 0; $i < count($files['tmp_name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $filename = basename($files['name'][$i]);
            $targetFile = $uploadDir . time() . '_' . uniqid() . '_' . $filename;

            if (move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
                $uploadedFiles[] = basename($targetFile);
            }
        }
    }

    return json_encode($uploadedFiles);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $naam = isset($_POST['naam']) ? trim($_POST['naam']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $locatie = isset($_POST['locatie']) ? trim($_POST['locatie']) : '';
    $aantal_tegels = isset($_POST['aantal_tegels']) ? (int)$_POST['aantal_tegels'] : 0;
    $beschrijving = isset($_POST['beschrijving']) ? trim($_POST['beschrijving']) : '';

    if (!$naam || !$email || !$locatie || $aantal_tegels <= 0 || !$beschrijving) {
        die("Vul alle verplichte velden correct in.");
    }

    $fotos_json = null;
    if (!empty($_FILES['fotos']) && is_array($_FILES['fotos']['tmp_name'])) {
        $fotos_json = uploadFotos($_FILES['fotos']);
    }

    try {
        $sql = "INSERT INTO meld_acties (naam, email, locatie, aantal_tegels, beschrijving, fotos) 
                VALUES (:naam, :email, :locatie, :aantal_tegels, :beschrijving, :fotos)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':locatie', $locatie);
        $stmt->bindParam(':aantal_tegels', $aantal_tegels, PDO::PARAM_INT);
        $stmt->bindParam(':beschrijving', $beschrijving);
        $stmt->bindParam(':fotos', $fotos_json);

        $stmt->execute();

        header("Location: bedankt.html");
        exit;

    } catch (PDOException $e) {
        die("Er is een fout opgetreden bij het opslaan: " . $e->getMessage());
    }

} else {
    die("Ongeldige toegang.");
}
