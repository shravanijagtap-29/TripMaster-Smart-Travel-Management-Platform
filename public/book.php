<?php
session_start();
require '../src/config.php'; // using your config file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // fetch destinations for dropdown
    $stmt = $pdo->query("SELECT id, name FROM destinations");
    $destinations = $stmt->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $destination_id = $_POST['destination_id'];
        $date = $_POST['date'];
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, destination_id, booking_date) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $destination_id, $date]);

        header("Location: my_bookings.php");
        exit();
    }
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Destination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Book a Destination</h2>
    <form method="POST" class="form">
        <div class="mb-3">
            <label class="form-label">Select Destination</label>
            <select name="destination_id" class="form-control" required>
                <option value="">-- Choose --</option>
                <?php foreach ($destinations as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Travel Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Book Now</button>
    </form>
</body>
</html>