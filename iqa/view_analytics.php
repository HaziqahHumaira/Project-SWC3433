<?php
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login if not logged in
    header('Location: admin_login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "syakila03";
$dbname = "admins";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total number of orders
$orderQuery = "SELECT COUNT(*) AS total_orders FROM orders";
$orderResult = $conn->query($orderQuery);
$totalOrders = 0;
if ($orderResult && $orderResult->num_rows > 0) {
    $row = $orderResult->fetch_assoc();
    $totalOrders = $row['total_orders'];
}

// Get total revenue
$revenueQuery = "SELECT SUM(total_amount) AS total_revenue FROM orders";
$revenueResult = $conn->query($revenueQuery);
$totalRevenue = 0.0;
if ($revenueResult && $revenueResult->num_rows > 0) {
    $row = $revenueResult->fetch_assoc();
    $totalRevenue = $row['total_revenue'];
}

// Get most popular item
$popularItemQuery = "SELECT item_id, COUNT(item_id) AS item_count FROM order_items GROUP BY item_id ORDER BY item_count DESC LIMIT 1";
$popularItemResult = $conn->query($popularItemQuery);
$mostPopularItem = "N/A";
if ($popularItemResult && $popularItemResult->num_rows > 0) {
    $row = $popularItemResult->fetch_assoc();
    $itemId = $row['item_id'];
    
    // Get item name from the menu_items table
    $itemQuery = "SELECT name FROM menu_items WHERE item_id = '$itemId'";
    $itemResult = $conn->query($itemQuery);
    if ($itemResult && $itemResult->num_rows > 0) {
        $itemRow = $itemResult->fetch_assoc();
        $mostPopularItem = $itemRow['name'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Analytics - Fantasy Dessert Shop</title>
    <style>
        /* General Reset */
        body, h1, h2, h3, p, ul, li, a, button {
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

        /* Analytics Section */
        .analytics {
            margin-top: 30px;
        }

        .analytics h2 {
            font-family: 'Fruktur', serif;
            font-size: 28px;
            color: #b84c65;
            margin-bottom: 20px;
        }

        .analytics-card {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .analytics-card h3 {
            font-size: 20px;
            color: #b84c65;
            margin-bottom: 10px;
        }

        .analytics-card p {
            font-size: 16px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo">
            <h1>Analytics Overview</h1>
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

    <!-- Analytics Section -->
    <section class="analytics">
        <h2>Analytics Overview</h2>
        
        <!-- Total Orders -->
        <div class="analytics-card">
            <h3>Total Orders</h3>
            <p>Number of orders: <?php echo $totalOrders; ?></p>
        </div>

        <!-- Total Revenue -->
        <div class="analytics-card">
            <h3>Total Revenue</h3>
            <p>Total revenue generated: RM <?php echo number_format($totalRevenue, 2); ?></p>
        </div>

        <!-- Most Popular Item -->
        <div class="analytics-card">
            <h3>Most Popular Item</h3>
            <p>Most ordered item: <?php echo htmlspecialchars($mostPopularItem); ?></p>
        </div>
    </section>
</body>
</html>
