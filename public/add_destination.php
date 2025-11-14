<?php
session_start();
require '../src/config.php';

if (!isset($_SESSION['user_id'])) {
    die("⚠ Please login first to add destinations.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // ✅ Handle Image Upload
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("INSERT INTO destinations (name, description, price, image, address, latitude, longitude) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $desc, $price, $imageName, $address, $latitude, $longitude])) {
            $success = "✅ Destination added successfully!";
        } else {
            $error = "❌ Failed to add destination.";
        }
    } else {
        $error = "❌ Failed to upload image.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Destination</title>
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
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            text-align: center;
            padding: 15px;
        }

        .form-control, .form-control-file {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-primary {
            border-radius: 10px;
            font-weight: 500;
            padding: 12px;
            width: 100%;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #0094cc);
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
        <div class="card-header">Add New Destination</div>
        <div class="card-body">

            <!-- ✅ Success/Error Messages -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Destination Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter destination name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Enter description"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price">
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Enter address">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" placeholder="Enter latitude">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" placeholder="Enter longitude">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary">➕ Add Destination</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>