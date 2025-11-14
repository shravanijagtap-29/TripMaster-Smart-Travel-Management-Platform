<?php
$host = "127.0.0.1";   // use 127.0.0.1 instead of localhost
$port = "3307";        // change to 3307 if your MySQL runs on 3307
$db   = "travelapp";   // database name
$user = "root";        // default XAMPP user
$pass = "";            // default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>