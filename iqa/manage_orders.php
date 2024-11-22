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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Fantasy Dessert Shop</title>
    <style>
        /* General Reset */
        body, h1, h2, h3, p, ul, li, a, button, table, th, td {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        /* Header Styling */
        .header {
            background-color: #b84c65;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-family: 'Arvo', serif;
            font-size: 20px;
        }

        .navbar ul {
            list-style: none;
            display: flex;
        }

        .navbar ul li {
            margin-right: 15px;
        }

        .navbar ul li a {
            color: #ffe600;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
        }

        /* Orders Section Styling */
        .orders {
            margin-top: 30px;
        }

        .orders h2 {
            font-family: 'Fruktur', serif;
            font-size: 28px;
            color: #b84c65;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #b84c65;
            color: #fff;
            font-family: 'Arvo', serif;
        }

        td {
            background-color: #fff;
            color: #333;
        }

        /* Action Buttons */
        .action-btn {
            background-color: #b84c65;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 14px;
        }

        .action-btn:hover {
            background-color: #8e4aad;
        }

        .completed {
            background-color: #4caf50;
        }

        .completed:hover {
            background-color: #388e3c;
        }

        /* Status Message Styling */
        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .status-message {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo">
            <h1 style="font-size: 20px;">Manage Orders</h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="view_analytics.php">View Analytics</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Status Message Section -->
    <div class="status-message">
        <?php
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            if ($status == 'completed') {
                echo "<p class='success'>Order marked as completed successfully.</p>";
            } elseif ($status == 'deleted') {
                echo "<p class='success'>Order deleted successfully.</p>";
            } elseif ($status == 'delete_failed') {
                echo "<p class='error'>Failed to delete order. Please try again.</p>";
            } elseif ($status == 'update_failed') {
                echo "<p class='error'>Failed to mark the order as completed. Please try again.</p>";
            } elseif ($status == 'insufficient_stock') {
                echo "<p class='error'>Failed to mark order as completed due to insufficient stock. Please restock items.</p>";
            }
        }
        ?>
    </div>

    <!-- Manage Orders Section -->
    <section class="orders">
        <h2>Order List</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer Phone</th>
                    <th>Customer Email</th>
                    <th>Items Ordered</th>
                    <th>Total Price (RM)</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch orders from the database
                $sql = "SELECT * FROM orders";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['order_id'] . "</td>";
                        echo "<td>" . $row['customer_name'] . "</td>";
                        echo "<td>" . $row['customer_phone'] . "</td>";
                        echo "<td>" . $row['customer_email'] . "</td>";

                        // Fetch items from order_items table for this order
                        $orderId = $row['order_id'];
                        $itemsSql = "SELECT item_id, quantity FROM order_items WHERE order_id = ?";
                        $itemsStmt = $conn->prepare($itemsSql);
                        $itemsStmt->bind_param("i", $orderId);
                        $itemsStmt->execute();
                        $itemsResult = $itemsStmt->get_result();

                        $itemsDetails = [];
                        while ($itemRow = $itemsResult->fetch_assoc()) {
                            $itemId = $itemRow['item_id'];
                            $quantity = $itemRow['quantity'];

                            // Fetch item name from menu_items
                            $menuItemSql = "SELECT name FROM menu_items WHERE item_id = ?";
                            $menuItemStmt = $conn->prepare($menuItemSql);
                            $menuItemStmt->bind_param("i", $itemId);
                            $menuItemStmt->execute();
                            $menuItemResult = $menuItemStmt->get_result();
                            $menuItemRow = $menuItemResult->fetch_assoc();

                            $itemsDetails[] = $menuItemRow['name'] . " (x" . $quantity . ")";
                        }
                        echo "<td>" . implode(", ", $itemsDetails) . "</td>";

                        // Display other order details
                        echo "<td>RM " . number_format($row['total_amount'], 2) . "</td>";
                        echo "<td>" . $row['order_date'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>";
                        // Mark as completed button
                        if ($row['status'] != 'Completed') {
                            echo "<button class='action-btn completed' onclick=\"markCompleted('" . $row['order_id'] . "')\">Mark as Completed</button>";
                        }
                        // Delete button
                        echo "<button class='action-btn' onclick=\"deleteOrder('" . $row['order_id'] . "')\">Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <script>
        // Function to mark an order as completed
        function markCompleted(orderId) {
            if (confirm('Are you sure you want to mark this order as completed?')) {
                window.location.href = 'mark_order_completed.php?id=' + orderId;
            }
        }

        // Function to delete an order
        function deleteOrder(orderId) {
            if (confirm('Are you sure you want to delete this order?')) {
                window.location.href = 'delete_order.php?id=' + orderId;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
