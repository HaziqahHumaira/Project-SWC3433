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

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete items associated with the order from order_items table
        $deleteItemsSql = "DELETE FROM order_items WHERE order_id = ?";
        $deleteItemsStmt = $conn->prepare($deleteItemsSql);
        $deleteItemsStmt->bind_param("i", $orderId);
        if (!$deleteItemsStmt->execute()) {
            throw new Exception("Failed to delete items for order ID: $orderId");
        }

        // Delete the order from orders table
        $deleteOrderSql = "DELETE FROM orders WHERE order_id = ?";
        $deleteOrderStmt = $conn->prepare($deleteOrderSql);
        $deleteOrderStmt->bind_param("i", $orderId);
        if (!$deleteOrderStmt->execute()) {
            throw new Exception("Failed to delete order ID: $orderId");
        }

        // Commit transaction
        $conn->commit();

        // Redirect with success message
        header('Location: manage_orders.php?status=deleted');
        exit();
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();

        // Redirect with failure message
        header('Location: manage_orders.php?status=delete_failed');
        exit();
    }
} else {
    // Redirect if no ID is provided
    header('Location: manage_orders.php');
    exit();
}

$conn->close();
?>
