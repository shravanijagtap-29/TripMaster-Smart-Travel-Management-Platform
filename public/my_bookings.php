<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../src/config.php';

try {
    $stmt = $pdo->prepare("SELECT b.id, d.name, d.price, b.booking_date, b.status
                           FROM bookings b
                           JOIN destinations d ON b.destination_id = d.id
                           WHERE b.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll();

    // ✅ Auto-update status if travel date < today
    $today = date("Y-m-d");
    foreach ($bookings as &$b) {
        if ($b['booking_date'] < $today) {
            $b['status'] = "Completed";
        }
    }
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>My Bookings</h2>
    <a href="index.php" class="btn btn-primary mb-3">Back to Dashboard</a>
    <?php if (empty($bookings)): ?>
        <p>No bookings found. <a href="book.php">Book a destination now</a>.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Destination</th>
                    <th>Price</th>
                    <th>Travel Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['name']) ?></td>
                        <td>$<?= htmlspecialchars($b['price']) ?></td>
                        <td><?= htmlspecialchars($b['booking_date']) ?></td>
                        <td><?= htmlspecialchars($b['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>