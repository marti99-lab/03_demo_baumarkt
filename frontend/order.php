<?php
session_start();
$pageTitle = "Bestellung aufgeben";
include "./components/header.php";
require_once '../backend/config/db.php';

$userAddress = "";
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT address FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['address'])) {
            $userAddress = $user['address'];
        }
    } catch (PDOException $e) {
        die("Datenbankfehler: " . $e->getMessage());
    }
}

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$productId) {
    echo "<p>Ungültige Produkt-ID.</p>";
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<p>Produkt nicht gefunden.</p>";
        exit;
    }
} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const orderForm = document.getElementById("order-form");

        orderForm.addEventListener("submit", function (event) {
            const address = document.getElementById("address").value.trim();

            // Regular expression to check German address pattern: "Street 123, 12345 City"
            const addressPattern = /^.+\s\d+(,\s\d{5}\s[A-Za-zäöüÄÖÜß\s-]+)?$/;

            if (!addressPattern.test(address)) {
                event.preventDefault();
                alert("Bitte geben Sie eine gültige Lieferadresse an (z.B. Musterstraße 12, 12345 Musterstadt).");
            }
        });
    });
</script>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellung aufgeben</title>
    <link rel="stylesheet" href="./assets/css/order-styles.css">
</head>
<body>
    <main>
        <section id="order-section">
            <h2>Bestellung für <?php echo htmlspecialchars($product['name']); ?></h2>

            <div class="order-details">
                <p><strong>Produkt:</strong> <?php echo htmlspecialchars($product['name']); ?></p>
                <p><strong>Preis:</strong> <?php echo htmlspecialchars($product['price']); ?> €</p>
                <p><strong>Verfügbarkeit:</strong> <?php echo htmlspecialchars($product['availability']); ?></p>
                <p><strong>Lieferzeit:</strong> <?php echo htmlspecialchars($product['lieferzeit']); ?></p>
            </div>

            <form id="order-form" action="../backend/api/order-handler.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">

                <label for="quantity">Menge:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" 
                    max="<?php echo $product['availability']; ?>" required>
                <p id="total-cost">Gesamtpreis: <?php echo number_format($product['price'], 2); ?> €</p>

                <label for="address">Lieferadresse:</label>
                <textarea id="address" name="address" placeholder="Ihre Lieferadresse" required><?php echo htmlspecialchars(trim($userAddress) ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

                <button type="submit" class="btn-order">Bestellung aufgeben</button>
            </form>
        </section>
    </main>

    <?php include "./components/footer.php"; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const quantityInput = document.getElementById("quantity");
        const totalCostDisplay = document.getElementById("total-cost");
        const productPrice = <?php echo $product['price']; ?>;

        quantityInput.addEventListener("input", function () {
            let quantity = parseInt(quantityInput.value) || 1;
            if (quantity > <?php echo $product['availability']; ?>) {
                quantity = <?php echo $product['availability']; ?>;
                quantityInput.value = quantity;
            }
            if (quantity < 1) {
                quantity = 1;
                quantityInput.value = quantity;
            }

            const totalCost = (quantity * productPrice).toFixed(2);
            totalCostDisplay.textContent = `Gesamtpreis: ${totalCost} €`;
        });
    });
    </script>
</body>
</html>
