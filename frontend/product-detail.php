<?php
$pageTitle = "Produktdetails";
include "components/header.php";

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    echo "<p>Ungültige Produkt-ID.</p>";
    exit;
}

try {
    require_once '../backend/config/db.php';
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<p>Produkt nicht gefunden.</p>";
        exit;
    }

    $isReservable = ($product['online_verfügbar'] === 'ja');
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
    <link rel="stylesheet" href="assets/css/styles.css">
    <script defer src="assets/js/product-view.js"></script>
</head>

<body>
    <main>
        <button class="btn-back" id="back-button">Zurück</button>
        <section id="product-detail">
            <div class="product-container">
                <div class="product-details">
                    <img id="product-image" src="assets/images/placeholder.jpg" alt="Produktbild">
                    <h2 id="product-title">Produktname</h2>
                    <p id="product-description">Kategorie: <span id="product-category"></span></p>
                    <p id="availability"></p>
                    <p id="delivery-time"></p>
                    <p id="shipping-info"></p>
                    <p id="discount-info"></p>
                    <div class="button-container">
                        <button class="btn" id="online-order"
                            onclick="window.location.href='order.php?id=<?php echo $productId; ?>'">
                            Online bestellen
                        </button>

                        <button class="btn" id="reserve-pickup">Reservieren & Abholen</button>
                    </div>

                </div>
            </div>
            <div class="additional-info">
                <h3>Produktbeschreibung:</h3>
                <p id="additional-text">Keine weiteren Informationen verfügbar.</p>
            </div>
        </section>
    </main>
</body>

<!-- Reservation Modal -->
<div id="reservation-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Reservierung bestätigen</h2>
        <p>Hallo, wie viele Einheiten möchten Sie reservieren?</p>

        <label for="reservation-quantity">Menge:</label>
        <input type="number" id="reservation-quantity" min="1" max="<?php echo htmlspecialchars($product['availability']); ?>" value="1">
        
        <p id="reservation-total-cost">Gesamtpreis: <?php echo number_format($product['price'], 2); ?> €</p>
        
        <p style="font-weight: bold; color: #d9534f;">Bitte holen Sie die Ware innerhalb von 3 Tagen ab und bezahlen Sie diese, da ansonsten die Reservierung verfällt.</p>
        
        <input type="hidden" id="product-id" value="<?php echo $productId; ?>">
        <input type="hidden" id="user-id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
        <input type="hidden" id="product-price" value="<?php echo $product['price']; ?>">

        <button id="confirm-reservation" class="btn">Reservierung bestätigen</button>
    </div>
</div>

</html>
<?php include "components/footer.php"; ?>