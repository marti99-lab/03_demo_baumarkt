<?php
session_start();

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('X-Content-Type-Options: nosniff');
header_remove('Pragma');
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? "Baumarkt-App"; ?></title>
    <?php if ($pageTitle === "Registrierung"): ?>
        <link rel="stylesheet" href="assets/css/reg-styles.css">
        <script defer src="assets/js/register.js"></script>
    <?php elseif ($pageTitle === "Baumarkt-App"): ?>
        <link rel="stylesheet" href="assets/css/styles.css">
        <script defer src="assets/js/scripts.js"></script>
    <?php elseif ($pageTitle === "Login"): ?>
        <link rel="stylesheet" href="assets/css/log-styles.css">
        <script defer src="assets/js/login.js"></script>
    <?php else: ?>
        <link rel="stylesheet" href="assets/css/styles.css">
    <?php endif; ?>
</head>
<body>
<header>
    <h1>Ihr Fix & Fertig Baumarkt – Lösungen für Haus & Garten!</h1>
    <nav>
        <ul>
            <li><a href="./#produkte">Produkte</a></li>
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['username'])): ?>
                <li>User: <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                <li><a href="./profil.php">Profil</a></li>
                <li><a href="./logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="register-page.php">Register</a></li>
                <li><a href="login-page.php">Login</a></li>
            <?php endif; ?>
            <li><a href="https://learn-it-bonn.de/">Zurück zur Hauptseite</a></li>
        </ul>
    </nav>
</header>
    <?php if ($pageTitle === "Baumarkt-App"): ?>
        <div id="rabatt-modal" class="modal">
            <div class="modal-content">
                <span class="close-button">&times;</span>
                <h2>Tolle Rabattaktionen mit 25%!</h2>
                <div id="rabatt-list"></div>
            </div>
        </div>
    <?php endif; ?>
