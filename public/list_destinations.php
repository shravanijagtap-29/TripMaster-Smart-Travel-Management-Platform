<?php
session_start();
require '../src/config.php';

// Fetch destinations
$stmt = $pdo->query("SELECT * FROM destinations ORDER BY created_at DESC");
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['destination_id'])) {
    if (!isset($_SESSION['user_id'])) {
        die("Please login to submit a review!");
    }

    $destination_id = $_POST['destination_id'];
    $review_text = $_POST['review_text'];
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
    <title>All Destinations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Leaflet CSS (for free maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        .map-container {
            height: 300px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="container mt-4">

    <h2>All Destinations</h2>

    <!-- ✅ Success Alert -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Your review has been submitted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php foreach ($destinations as $d): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($d['name']) ?></h4>
                <p><strong>Price:</strong> $<?= htmlspecialchars($d['price']) ?></p>
                <p><?= nl2br(htmlspecialchars($d['description'])) ?></p>

                <!-- ✅ Show uploaded image -->
                <?php if (!empty($d['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($d['image']) ?>" 
                         alt="Destination Image" 
                         class="img-fluid mb-3" 
                         style="max-height:300px;">
                <?php endif; ?>

                <!-- ✅ Show address -->
                <?php if (!empty($d['address'])): ?>
                    <p><strong>Address:</strong> <?= htmlspecialchars($d['address']) ?></p>
                <?php endif; ?>

                <!-- ✅ Leaflet Map -->
                <?php if (!empty($d['latitude']) && !empty($d['longitude'])): ?>
                    <div id="map-<?= $d['id'] ?>" class="map-container"></div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var map = L.map("map-<?= $d['id'] ?>").setView([<?= $d['latitude'] ?>, <?= $d['longitude'] ?>], 13);
                            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                                attribution: "&copy; OpenStreetMap contributors"
                            }).addTo(map);
                            L.marker([<?= $d['latitude'] ?>, <?= $d['longitude'] ?>])
                                .addTo(map)
                                .bindPopup("<?= htmlspecialchars($d['name']) ?>");
                        });
                    </script>
                <?php endif; ?>

                <hr>

                <!-- ✅ Reviews -->
                <h5>Reviews:</h5>
                <?php
                $stmt = $pdo->prepare("SELECT r.comment, r.rating, r.created_at, u.name 
                                       FROM reviews r 
                                       JOIN users u ON r.user_id = u.id 
                                       WHERE r.destination_id = ? 
                                       ORDER BY r.created_at DESC");
                $stmt->execute([$d['id']]);
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($reviews):
                    foreach ($reviews as $rev): ?>
                        <div class="border rounded p-2 mb-2">
                            <strong><?= htmlspecialchars($rev['name']) ?></strong> 
                            <span>(⭐ <?= $rev['rating'] ?>/5)</span><br>
                            <?= htmlspecialchars($rev['comment']) ?><br>
                            <small class="text-muted"><?= $rev['created_at'] ?></small>
                        </div>
                    <?php endforeach;
                else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>

    <!-- ✅ Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>