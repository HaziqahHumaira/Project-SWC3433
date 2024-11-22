<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: admin_login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "syakila03";
$dbname = "admins";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    $sql = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        // Redirect with success message
        header('Location: manage_orders.php?status=deleted');
        exit();
    } else {
        // Redirect with error message
        header('Location: manage_orders.php?status=delete_failed');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
