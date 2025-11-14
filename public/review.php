<?php
session_start();
require '../src/config.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first!");
}

// Fetch destinations for dropdown
$stmt = $pdo->query("SELECT id, name FROM destinations ORDER BY name ASC");
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination_id = $_POST['destination_id'];
    $review_text = $_POST['review_text']; // ✅ Fix: use same name as textarea
    $rating = $_POST['rating'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, destination_id, comment, rating) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $destination_id, $review_text, $rating]);

    header("Location: list_destinations.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Submit a Review</h2>
    <form method="POST" class="form">
        <!-- Destination Dropdown -->
        <div class="mb-3">
            <label class="form-label">Destination</label>
            <select name="destination_id" class="form-control" required>
                <option value="">-- Select Destination --</option>
                <?php foreach ($destinations as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Rating (Stars 1–5) -->
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-control" required>
                <option value="">-- Select Rating --</option>
                <option value="1">⭐ 1 - Poor</option>
                <option value="2">⭐⭐ 2 - Fair</option>
                <option value="3">⭐⭐⭐ 3 - Good</option>
                <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
            </select>
        </div>

        <!-- Review Comment -->
        <div class="mb-3">
            <label class="form-label">Your Review</label>
            <!-- ✅ FIXED: name must match "review_text" -->
            <textarea name="review_text" class="form-control" rows="3" placeholder="Write your review..." required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Review</button>
    </form>
</body>
</html>