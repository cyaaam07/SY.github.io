<?php
// auth_check.php - Include dit bestand op pagina's die ingelogde gebruikers vereisen

session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: inloggen.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser($conn) {
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function redirectIfLoggedIn($redirect_to = 'account.php') {
    if (isLoggedIn()) {
        header("Location: $redirect_to");
        exit;
    }
}
?>