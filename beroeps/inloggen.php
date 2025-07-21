<?php
require_once 'config.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    if (empty($email)) $errors[] = "E-mailadres is verplicht";
    if (empty($wachtwoord)) $errors[] = "Wachtwoord is verplicht";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, voornaam, achternaam, email, wachtwoord FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($wachtwoord, $user['wachtwoord'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['voornaam'] . ' ' . $user['achternaam'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: account.php');
            exit;
        } else {
            $errors[] = "Ongeldig e-mailadres of wachtwoord";
        }
    }
}

$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Tegelwip Atlas</title>
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
                <li><a href="registreren.php">Registreren</a></li>
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
            <h2>Inloggen</h2>

            <?php if ($success_message): ?>
                <div class="success-message">
                    <p><?= htmlspecialchars($success_message) ?></p>
                </div>
            <?php endif; ?>

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
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="wachtwoord">Wachtwoord</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" required>
                </div>
                <br>

                <button type="submit" class="button-primary">Inloggen</button>
            </form>

            <p class="form-footer">
                Nog geen account? <a href="registreren.php">Registreer hier</a>
            </p>
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