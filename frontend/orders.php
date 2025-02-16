<?php
$pageTitle = "Bestellübersicht";
include "./components/header.php";
require_once '../backend/config/db.php';

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

if (!$orderId) {
    echo "<p>Ungültige Bestellnummer.</p>";
    exit;
}

try {
    // Fetch order details
    $stmt = $conn->prepare("
        SELECT o.id AS order_id, p.name AS product_name, o.quantity, o.total_price, o.address, o.order_date
        FROM orders o
        JOIN products p ON o.product_id = p.id
        WHERE o.id = :order_id
    ");
    $stmt->execute([':order_id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<p>Bestellung nicht gefunden.</p>";
        exit;
    }
} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/styles.css">
</head>
<body>
    <main>
        <section id="order-summary">
            <h2>Bestellübersicht</h2>
            <p><strong>Bestellnummer:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
            <p><strong>Produkt:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
            <p><strong>Menge:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
            <p><strong>Gesamtpreis:</strong> <?php echo number_format($order['total_price'], 2); ?> €</p>
            <p><strong>Lieferadresse:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Bestelldatum:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        </section>
        <a href="index.php" class="btn">Zurück zur Startseite</a>
    </main>
</body>
</html>
