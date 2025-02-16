<?php
session_start();
$pageTitle = "Profil";
include "./components/header.php";
require_once '../backend/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login-page.php');
    exit;
}

$userId = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare("SELECT username, email, address FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="assets/css/profil-styles.css">
</head>

<body>
    <main>
        <section id="profile-section">
            <?php
            if (isset($_SESSION['profile_update_success'])) {
                echo "<p style='color: green; font-weight: bold;'>{$_SESSION['profile_update_success']}</p>";
                unset($_SESSION['profile_update_success']);
            }

            if (isset($_SESSION['profile_update_error'])) {
                echo "<p style='color: red; font-weight: bold;'>{$_SESSION['profile_update_error']}</p>";
                unset($_SESSION['profile_update_error']);
            }
            ?>

            <h2>Profil von <?php echo htmlspecialchars($user['username']); ?></h2>

            <h3>Profil aktualisieren</h3>
            <form id="update-profile-form" action="../backend/api/update-profile.php" method="POST">
                <label for="email">E-Mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                    required>

                <label for="address">Adresse (optional):</label>
                <textarea id="address" name="address"
                    placeholder="Ihre Lieferadresse"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>

                <label for="password">Neues Passwort (optional):</label>
                <input type="password" id="password" name="password" placeholder="Neues Passwort">

                <button type="submit" class="btn-update">Aktualisieren</button>
            </form>
            <h3>Ihre Reservierungen</h3>
            <table id="reservation-table">
                <thead>
                    <tr>
                        <th>Reservierungs-ID</th>
                        <th>Produkt</th>
                        <th>Menge</th>
                        <th>Gesamtpreis</th>
                        <th>Reservierungsdatum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->prepare("
                SELECT r.id AS reservation_id, r.quantity, r.reservation_date, p.name AS product_name, p.price 
                FROM reservations r
                JOIN products p ON r.product_id = p.id
                WHERE r.user_id = :user_id
                ORDER BY r.reservation_date DESC
            ");
                        $stmt->execute([':user_id' => $userId]);
                        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($reservations) {
                            foreach ($reservations as $reservation) {
                                $totalCost = $reservation['quantity'] * $reservation['price'];
                                echo "<tr>
                            <td>{$reservation['reservation_id']}</td>
                            <td>" . htmlspecialchars($reservation['product_name']) . "</td>
                            <td>{$reservation['quantity']}</td>
                            <td>" . number_format($totalCost, 2) . " €</td>
                            <td>{$reservation['reservation_date']}</td>
                          </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Keine Reservierungen gefunden.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5'>Datenbankfehler: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h3>Ihre Bestellungen</h3>
            <ul id="order-list">
                <?php
                try {
                    $stmt = $conn->prepare("
                        SELECT o.id AS order_id, o.quantity, o.total_price, o.order_date, p.name AS product_name 
                        FROM orders o
                        JOIN products p ON o.product_id = p.id
                        WHERE o.user_id = :user_id
                        ORDER BY o.order_date DESC
                    ");
                    $stmt->execute([':user_id' => $userId]);
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($orders) {
                        foreach ($orders as $order) {
                            echo "<li>
                                    <strong>Bestellung #{$order['order_id']}</strong> - 
                                    Produkt: <strong>" . htmlspecialchars($order['product_name']) . "</strong> - 
                                    Menge: <strong>{$order['quantity']}</strong> - 
                                    Gesamtpreis: <strong>" . number_format($order['total_price'], 2) . " €</strong> - 
                                    Datum: {$order['order_date']}
                                  </li>";
                        }
                    } else {
                        echo "<li>Keine Bestellungen gefunden.</li>";
                    }
                } catch (PDOException $e) {
                    echo "<li>Datenbankfehler: " . $e->getMessage() . "</li>";
                }
                ?>
            </ul>
        </section>
    </main>
</body>

</html>