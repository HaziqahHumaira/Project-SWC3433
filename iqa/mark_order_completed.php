<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: admin_login.php');
    exit();
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "syakila03";
$dbname = "admins";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Begin transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Fetch items in the order from `order_items` table
        $itemsSql = "SELECT item_id, quantity FROM order_items WHERE order_id = ?";
        $itemsStmt = $conn->prepare($itemsSql);
        $itemsStmt->bind_param("i", $orderId);
        $itemsStmt->execute();
        $result = $itemsStmt->get_result();

        // Loop through each item and check stock in `menu_items`
        while ($row = $result->fetch_assoc()) {
            $itemId = $row['item_id'];
            $quantity = $row['quantity'];

            // Check if there's enough stock available
            $stockCheckSql = "SELECT stock FROM menu_items WHERE item_id = ?";
            $stockCheckStmt = $conn->prepare($stockCheckSql);
            $stockCheckStmt->bind_param("i", $itemId);
            $stockCheckStmt->execute();
            $stockResult = $stockCheckStmt->get_result();
            $stockData = $stockResult->fetch_assoc();

            if ($stockData['stock'] < $quantity) {
                // Not enough stock, rollback and show an error message
                $conn->rollback();
                header('Location: manage_orders.php?status=insufficient_stock');
                exit();
            }
        }

        // Update the order status to 'Completed'
        $sql = "UPDATE orders SET status = 'Completed' WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        // Loop through each item and update stock in `menu_items`
        $result->data_seek(0); // Reset result pointer
        while ($row = $result->fetch_assoc()) {
            $itemId = $row['item_id'];
            $quantity = $row['quantity'];

            // Update stock in the `menu_items` table
            $stockSql = "UPDATE menu_items SET stock = stock - ? WHERE item_id = ?";
            $stockStmt = $conn->prepare($stockSql);
            $stockStmt->bind_param("ii", $quantity, $itemId);
            $stockStmt->execute();
        }

        // Commit the transaction
        $conn->commit();

        // Redirect to manage orders page with success message
        header('Location: manage_orders.php?status=completed');
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of any error
        $conn->rollback();
        // Redirect to manage orders page with error message
        header('Location: manage_orders.php?status=update_failed');
        exit();
    }

    $stmt->close();
    $itemsStmt->close();
}

$conn->close();
?>
