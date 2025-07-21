<?php
require_once 'config.php';
session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validatie
    $voornaam = trim($_POST['voornaam'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
    $wachtwoord_bevestig = $_POST['wachtwoord_bevestig'] ?? '';
    $telefoon = trim($_POST['telefoon'] ?? '');
    $adres = trim($_POST['adres'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $woonplaats = trim($_POST['woonplaats'] ?? '');
    $geboortedatum = $_POST['geboortedatum'] ?? '';


    if (empty($voornaam)) $errors[] = "Voornaam is verplicht";
    if (empty($achternaam)) $errors[] = "Achternaam is verplicht";
    if (empty($email)) $errors[] = "E-mailadres is verplicht";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Ongeldig e-mailadres";
    if (empty($wachtwoord)) $errors[] = "Wachtwoord is verplicht";
    if (strlen($wachtwoord) < 6) $errors[] = "Wachtwoord moet minimaal 6 karakters lang zijn";
    if ($wachtwoord !== $wachtwoord_bevestig) $errors[] = "Wachtwoorden komen niet overeen";


    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Dit e-mailadres is al geregistreerd";
        }
    }


    if (empty($errors)) {
        $wachtwoord_hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (voornaam, achternaam, email, wachtwoord, telefoon, adres, postcode, woonplaats, geboortedatum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt->execute([$voornaam, $achternaam, $email, $wachtwoord_hash, $telefoon, $adres, $postcode, $woonplaats, $geboortedatum])) {
            $success = true;
            $_SESSION['success_message'] = "Account succesvol aangemaakt! Je kunt nu inloggen.";
        } else {
            $errors[] = "Er ging iets mis bij het aanmaken van je account";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren - Tegelwip Atlas</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
<header>
    <div class="container">
        <h1><a href="/">Tegelwip Atlas</a></h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="inloggen.php">Inloggen</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>

    <section class="form-section">
        <div class="container">
            <br>
            <br>
            <br>
            <br>
            <br>

            <h2>Account Aanmaken</h2>


            <?php if ($success): ?>
                <div class="success-message">
                    <p>Account succesvol aangemaakt! <a href="inloggen.php">Klik hier om in te loggen</a></p>
                </div>
            <?php else: ?>

                <?php if (!empty($errors)): ?>
                    <div class="error-messages">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="user-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="voornaam">Voornaam *</label>
                            <input type="text" id="voornaam" name="voornaam" value="<?= htmlspecialchars($voornaam ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="achternaam">Achternaam *</label>
                            <input type="text" id="achternaam" name="achternaam" value="<?= htmlspecialchars($achternaam ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mailadres *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="wachtwoord">Wachtwoord *</label>
                            <input type="password" id="wachtwoord" name="wachtwoord" required>
                        </div>
                        <div class="form-group">
                            <label for="wachtwoord_bevestig">Bevestig Wachtwoord *</label>
                            <input type="password" id="wachtwoord_bevestig" name="wachtwoord_bevestig" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telefoon">Telefoonnummer</label>
                        <input type="tel" id="telefoon" name="telefoon" value="<?= htmlspecialchars($telefoon ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="adres">Adres</label>
                        <input type="text" id="adres" name="adres" value="<?= htmlspecialchars($adres ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" id="postcode" name="postcode" value="<?= htmlspecialchars($postcode ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="woonplaats">Woonplaats</label>
                            <input type="text" id="woonplaats" name="woonplaats" value="<?= htmlspecialchars($woonplaats ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="geboortedatum">Geboortedatum</label>
                        <input type="date" id="geboortedatum" name="geboortedatum" value="<?= htmlspecialchars($geboortedatum ?? '') ?>">
                    </div>
<br>
                    <button type="submit" class="button-primary">Account Aanmaken</button>
                </form>

                <p class="form-footer">
                    Heb je al een account? <a href="inloggen.php">Log hier in</a>
                </p>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Tegelwip Atlas | Sihaam , Gabrielle , Manouk - D2C</p>
    </div>
</footer>
</body>
</html>