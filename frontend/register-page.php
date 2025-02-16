<?php
$pageTitle = "Registrierung";
include "components/header.php";
?>
<main>
    <section id="log-reg-section">
        <h2>Erstellen Sie ein Konto</h2>
        <form id="log-reg-form" method="POST" action="javascript:void(0);">
            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="email" name="email" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <button type="submit">Registrieren</button>
        </form>
        <div id="message"></div>
        <p>Haben Sie schon ein Konto? <a href="login-page.php">Hier anmelden</a>.</p>
    </section>
</main>
<?php include "components/footer.php"; ?>




