<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Travel App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="text-center mb-4">🌍 Welcome to TravelApp</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p class="text-success text-center">
            Logged in as <strong><?php echo $_SESSION['name']; ?></strong>
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="add_destination.php" class="btn btn-primary">Add Destination</a>
            <a href="book.php" class="btn btn-warning">Book Destination</a>
            <a href="review.php" class="btn btn-info">Write Review</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
            <a href="list_destinations.php" class="btn btn-secondary">View Destinations</a>
            <a href="my_bookings.php" class="btn btn-success">My Bookings</a>
            <a href="edit_destination.php" class="btn btn-primary">edit destination</a>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-center gap-3">
            <a href="register.php" class="btn btn-success">Register</a>
            <a href="login.php" class="btn btn-primary">Login</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>