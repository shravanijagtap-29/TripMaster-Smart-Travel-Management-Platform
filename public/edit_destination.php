<?php
session_start();
require '../src/config.php';

if (!isset($_SESSION['user_id'])) {
    die("⚠ Please login first to edit destinations.");
}

if (!isset($_GET['id'])) {
    die("⚠ Destination ID not provided.");
}

$id = $_GET['id'];

// ✅ Fetch existing destination
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$destination = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$destination) {
    die("⚠ Destination not found!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // ✅ Handle Image Upload (optional)
    $imageName = $destination['image']; // keep old image by default
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $error = "❌ Failed to upload image.";
        }
    }

    // ✅ Update destination
    $stmt = $pdo->prepare("UPDATE destinations 
                           SET name=?, description=?, price=?, image=?, address=?, latitude=?, longitude=? 
                           WHERE id=?");
    if ($stmt->execute([$name, $desc, $price, $imageName, $address, $latitude, $longitude, $id])) {
        $success = "✅ Destination updated successfully!";
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$id]);
        $destination = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "❌ Failed to update destination.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Destination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7f9;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 700px;
            margin-top: 40px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            text-align: center;
            padding: 15px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-success {
            border-radius: 10px;
            font-weight: 500;
            padding: 12px;
            width: 100%;
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #1e7e34, #138f72);
        }

        .alert {
            border-radius: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">✏ Edit Destination</div>
        <div class="card-body">

            <!-- ✅ Messages -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Destination Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($destination['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($destination['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($destination['price']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Address</label>
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($destination['address']) ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($destination['latitude']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($destination['longitude']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <?php if (!empty($destination['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($destination['image']) ?>" alt="Current Image" class="img-fluid mb-2" style="max-height:150px;">
                    <?php else: ?>
                        <p>No image uploaded.</p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload New Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-success">💾 Update Destination</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>