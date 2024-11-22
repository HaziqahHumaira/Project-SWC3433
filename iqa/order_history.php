<?php
session_start();
include('config.php');  // Include your database connection file

// Check if the user is logged in, if not redirect to login page (if applicable)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch order history for the logged-in user
$user_id = $_SESSION['user_id']; // Assuming you store user_id in the session

$sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="order_history.php">Order History</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="order-history">
        <h2>Your Order History</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($order = $result->fetch_assoc()) {
                echo "<div class='order-item'>";
                echo "<p>Order ID: " . $order['order_id'] . "</p>";
                echo "<p>Order Date: " . $order['order_date'] . "</p>";
                echo "<p>Status: " . $order['status'] . "</p>";
                echo "<p>Total: RM " . number_format($order['total'], 2) . "</p>";
                echo "<a href='order_details.php?order_id=" . $order['order_id'] . "' class='view-details'>View Details</a>";
                echo "</div>";
            }
        } else {
            echo "<p>You haven't placed any orders yet.</p>";
        }
        ?>
    </section>

</body>
</html>

<?php
$conn->close();
?>
