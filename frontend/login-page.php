<?php 
$pageTitle = "Registrierung";
include "components/header.php";
?>
<main>
    <section id="log-reg-section">
        <h2>Melden Sie sich an</h2>
        <form id="log-reg-form">
            <input type="text" id="username" name="username" placeholder="Benutzername" required>
            <input type="password" id="password" name="password" placeholder="Passwort" required>
            <button type="submit">Anmelden</button>
        </form>
        <div id="message"></div>
        <p>Haben Sie noch kein Konto? <a href="register-page.php">Hier registrieren</a>.</p>
    </section>
</main>

<script>
    document.getElementById('log-reg-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(event.target);

        fetch('/baumarkt-app/backend/api/login-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Login fehlgeschlagen.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('message').innerText = 'Login erfolgreich! Sie werden weitergeleitet...';
                setTimeout(() => {
                    window.location.href = './index.php';
                }, 2000);
            } else {
                document.getElementById('message').innerText = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('message').innerText = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        });
    });
</script>
<?php include "components/footer.php"; ?>
