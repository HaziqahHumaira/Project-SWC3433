<?php
// Database connection
$host = 'localhost';
$dbname = 'iqa';
$username = 'root';
$password = 'syakila03';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Get the booking ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete booking from the database
    $query = "DELETE FROM bookings WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirect back to the dashboard after deleting
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "Invalid booking ID!";
}
