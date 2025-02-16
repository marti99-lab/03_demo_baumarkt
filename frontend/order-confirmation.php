<?php
session_start();
require_once '../backend/config/db.php';

$pageTitle = "Bestellung erfolgreich";

$productId = intval($_GET['product_id']);

if (!isset($_SESSION['user_id'])) {
    header('Location: login-page.php');
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT name, price, lieferzeit FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT quantity, total_price, address 
        FROM orders 
        WHERE user_id = :user_id AND product_id = :product_id 
        ORDER BY order_date DESC LIMIT 1
    ");
    $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    $username = $_SESSION['username'];

    if (!$product || !$order) {
        die('Fehler: Bestellung konnte nicht gefunden werden.');
    }

} catch (PDOException $e) {
    die('Datenbankfehler: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/order-styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let countdown = 30;
            const countdownDisplay = document.getElementById("countdown");
            const redirectUrl = `order.php?id=<?php echo $productId; ?>`;

            const timer = setInterval(() => {
                countdown--;
                countdownDisplay.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timer);
                    window.location.href = redirectUrl;
                }
            }, 1000);

            document.getElementById("manual-redirect").addEventListener("click", function () {
                window.location.href = redirectUrl;
            });
        });
    </script>
</head>

<body>
    <main>
        <section id="order-section">
            <h2>Bestellung erfolgreich!</h2>
            <p>Vielen Dank für Ihre Bestellung, <strong><?php echo htmlspecialchars($username); ?></strong>.</p>
            <p>Ihre Bestellung wird an folgende Adresse geliefert:</p>
            <p><strong><?php echo htmlspecialchars($order['address']); ?></strong></p>

            <h3>Details Ihrer Bestellung</h3>
            <table class="order-summary">
                <tr>
                    <th>Produkt:</th>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                </tr>
                <tr>
                    <th>Menge:</th>
                    <td><?php echo $order['quantity']; ?></td>
                </tr>
                <tr>
                    <th>Gesamtpreis:</th>
                    <td><?php echo number_format($order['total_price'], 2); ?> €</td>
                </tr>
                <tr>
                    <th>Lieferzeit:</th>
                    <td><?php echo htmlspecialchars($product['lieferzeit']); ?></td>
                </tr>
            </table>

            <p>Sie werden in <span id="countdown">30</span> Sekunden automatisch weitergeleitet.</p>
            <p>Falls die automatische Weiterleitung nicht funktioniert, klicken Sie bitte auf den folgenden Button:</p>
            <button id="manual-redirect" class="btn-order">Weiter</button>
        </section>
    </main>
</body>

</html>