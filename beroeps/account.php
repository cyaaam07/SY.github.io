<?php
require_once 'config.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: inloggen.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';


$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: inloggen.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {

        $voornaam = trim($_POST['voornaam'] ?? '');
        $achternaam = trim($_POST['achternaam'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefoon = trim($_POST['telefoon'] ?? '');
        $adres = trim($_POST['adres'] ?? '');
        $postcode = trim($_POST['postcode'] ?? '');
        $woonplaats = trim($_POST['woonplaats'] ?? '');
        $geboortedatum = $_POST['geboortedatum'] ?? '';


        if (empty($voornaam)) $errors[] = "Voornaam is verplicht";
        if (empty($achternaam)) $errors[] = "Achternaam is verplicht";
        if (empty($email)) $errors[] = "E-mailadres is verplicht";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Ongeldig e-mailadres";


        if (empty($errors) && $email !== $user['email']) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = "Dit e-mailadres is al in gebruik";
            }
        }


        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE users SET voornaam = ?, achternaam = ?, email = ?, telefoon = ?, adres = ?, postcode = ?, woonplaats = ?, geboortedatum = ? WHERE id = ?");

            if ($stmt->execute([$voornaam, $achternaam, $email, $telefoon, $adres, $postcode, $woonplaats, $geboortedatum, $user_id])) {
                $success_message = "Je gegevens zijn succesvol bijgewerkt!";
                $_SESSION['user_name'] = $voornaam . ' ' . $achternaam;
                $_SESSION['user_email'] = $email;


                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            } else {
                $errors[] = "Er ging iets mis bij het bijwerken van je gegevens";
            }
        }
    } elseif ($action === 'change_password') {

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password)) $errors[] = "Huidig wachtwoord is verplicht";
        if (empty($new_password)) $errors[] = "Nieuw wachtwoord is verplicht";
        if (strlen($new_password) < 6) $errors[] = "Nieuw wachtwoord moet minimaal 6 karakters lang zijn";
        if ($new_password !== $confirm_password) $errors[] = "Nieuwe wachtwoorden komen niet overeen";

        if (empty($errors)) {
            if (password_verify($current_password, $user['wachtwoord'])) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET wachtwoord = ? WHERE id = ?");

                if ($stmt->execute([$new_password_hash, $user_id])) {
                    $success_message = "Je wachtwoord is succesvol gewijzigd!";
                } else {
                    $errors[] = "Er ging iets mis bij het wijzigen van je wachtwoord";
                }
            } else {
                $errors[] = "Huidig wachtwoord is onjuist";
            }
        }
    } elseif ($action === 'delete_account') {

        $confirm_delete = $_POST['confirm_delete'] ?? '';

        if ($confirm_delete === 'DELETE') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$user_id])) {
                session_destroy();
                header('Location: index.html?deleted=1');
                exit;
            } else {
                $errors[] = "Er ging iets mis bij het verwijderen van je account";
            }
        } else {
            $errors[] = "Je moet 'DELETE' typen om je account te verwijderen";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Account - Tegelwip Atlas</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>

        .account-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #004d00;
            border-radius: 8px;
            background-color: #003300;
            color: #fff;
        }


        .account-info {
            background-color: #1b4d3e;
            color: #f8f9fa;
        }


        .password-section {
            background-color:  #2b5a47;;
            color: #333;
        }


        .danger-section {
            background-color: #360808;
            color: #fff;
        }


        .form-actions {
            margin-top: 1rem;
        }

        .form-actions button {
            margin-right: 0.5rem;
            background-color: #004d00;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-actions button:hover {
            background-color: #006600;
        }


        .user-info-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }


        .info-item {
            padding: 0.5rem;
            background: #2b5a47;
            color: #fff;
            border-radius: 4px;
        }


        .info-label {
            font-weight: bold;
            color: #d3d3d3;
            font-size: 0.9em;
        }


        .info-value {
            margin-top: 0.25rem;
            color: #eaeaea;
        }


        body {
            font-family: 'Arial', sans-serif;
        }

    </style>
</head>

<body>
<header>
    <div class="container">
        <h1><a href="/">Tegelwip Atlas</a></h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="uitloggen.php">Uitloggen</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="form-section">
        <div class="container">
            <h2>Welkom, <?= htmlspecialchars($user['voornaam']) ?>!</h2>

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


            <div class="account-section account-info">
                <h3>Jouw Gegevens</h3>
                <div class="user-info-display">
                    <div class="info-item">
                        <div class="info-label">Naam</div>
                        <div class="info-value"><?= htmlspecialchars($user['voornaam'] . ' ' . $user['achternaam']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">E-mail</div>
                        <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Telefoon</div>
                        <div class="info-value"><?= htmlspecialchars($user['telefoon'] ?: 'Niet ingevuld') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Adres</div>
                        <div class="info-value"><?= htmlspecialchars($user['adres'] ?: 'Niet ingevuld') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Postcode & Plaats</div>
                        <div class="info-value"><?= htmlspecialchars(($user['postcode'] ? $user['postcode'] . ' ' : '') . ($user['woonplaats'] ?: 'Niet ingevuld')) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Geboortedatum</div>
                        <div class="info-value"><?= $user['geboortedatum'] ? date('d-m-Y', strtotime($user['geboortedatum'])) : 'Niet ingevuld' ?></div>
                    </div>
                </div>

                <button type="button" onclick="toggleEdit()" class="button-secondary">Gegevens Bewerken</button>
            </div>


            <div id="edit-form" class="account-section" style="display: none;">
                <h3>Gegevens Bewerken</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="voornaam">Voornaam *</label>
                            <input type="text" id="voornaam" name="voornaam" value="<?= htmlspecialchars($user['voornaam']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="achternaam">Achternaam *</label>
                            <input type="text" id="achternaam" name="achternaam" value="<?= htmlspecialchars($user['achternaam']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mailadres *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefoon">Telefoonnummer</label>
                        <input type="tel" id="telefoon" name="telefoon" value="<?= htmlspecialchars($user['telefoon']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="adres">Adres</label>
                        <input type="text" id="adres" name="adres" value="<?= htmlspecialchars($user['adres']) ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" id="postcode" name="postcode" value="<?= htmlspecialchars($user['postcode']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="woonplaats">Woonplaats</label>
                            <input type="text" id="woonplaats" name="woonplaats" value="<?= htmlspecialchars($user['woonplaats']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="geboortedatum">Geboortedatum</label>
                        <input type="date" id="geboortedatum" name="geboortedatum" value="<?= htmlspecialchars($user['geboortedatum']) ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="button-primary">Opslaan</button>
                        <button type="button" onclick="toggleEdit()" class="button-secondary">Annuleren</button>
                    </div>
                </form>
            </div>


            <div class="account-section password-section">
                <h3>Wachtwoord Wijzigen</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label for="current_password">Huidig Wachtwoord</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">Nieuw Wachtwoord</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Bevestig Nieuw Wachtwoord</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <button type="submit" class="button-primary">Wachtwoord Wijzigen</button>
                </form>
            </div>


            <div class="account-section danger-section">
                <h3>Account Verwijderen</h3>
                <p><strong>Let op:</strong> Dit kan niet ongedaan gemaakt worden!</p>
                <form method="POST" onsubmit="return confirm('Weet je zeker dat je je account wilt verwijderen? Dit kan niet ongedaan gemaakt worden!')">
                    <input type="hidden" name="action" value="delete_account">

                    <div class="form-group">
                        <label for="confirm_delete">Typ 'DELETE' om te bevestigen:</label>
                        <input type="text" id="confirm_delete" name="confirm_delete" required>
                    </div>

                    <button type="submit" class="button-primary" style="background-color: #dc3545;">Account Verwijderen</button>
                </form>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Tegelwip Atlas | Sihaam , Gabrielle , Manouk - D2C</p>
    </div>
</footer>

<script>
    function toggleEdit() {
        const editForm = document.getElementById('edit-form');
        if (editForm.style.display === 'none') {
            editForm.style.display = 'block';
            editForm.scrollIntoView({ behavior: 'smooth' });
        } else {
            editForm.style.display = 'none';
        }
    }
</script>
</body>
</html>