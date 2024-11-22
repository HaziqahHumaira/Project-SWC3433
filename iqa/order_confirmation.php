<?php
session_start();

// Database connection details (change these to your settings)
$servername = "localhost";
$username = "root";
$password = "syakila03"; // Replace with your MySQL root password
$dbname = "admins"; // Replace with your database name

// Create a new connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the cart is not empty
if (empty($_SESSION['cart'])) {
    header('Location: menu.php');
    exit();
}

// Calculate total price
$total = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $total += $cart_item['total_price'];
}

// Handle form submission for order confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'] ?? 'Guest';
    $customer_phone = $_POST['customer_phone'] ?? 'Not Provided';
    $customer_email = $_POST['customer_email'] ?? 'guest@example.com';
    
    // Generate a valid datetime string in the required format
    $order_date = date("Y-m-d H:i:s"); // Correct format for MySQL datetime
    $status = 'Pending'; // Initial status is set to "Pending"

    // Insert the order into orders table
    $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, order_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssds", $customer_name, $customer_email, $customer_phone, $order_date, $total, $status);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Insert each item from the cart into the order_items table
        foreach ($_SESSION['cart'] as $cart_item) {
            $item_id = $cart_item['item_id'];
            $quantity = $cart_item['quantity'];
            $price = $cart_item['price'];

            $item_sql = "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param("iiid", $order_id, $item_id, $quantity, $price);
            $item_stmt->execute();
        }

        // Clear the cart after order is confirmed
        unset($_SESSION['cart']);

        // Redirect to menu page with a success message
        echo "<script>alert('Order placed successfully. Please wait for our response!'); window.location.href='menu.php';</script>";
        exit();
    } else {
        echo "Failed to place the order. Please try again.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Fantasy Dessert Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&family=Fruktur:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Caveat+Brush&display=swap" rel="stylesheet">
    <style>
        /* All your existing CSS here remains unchanged */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .order-summary {
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .order-summary h2 {
            font-family: 'Caveat Brush', cursive;
            font-size: 24px;
            color: #b84c65;
            margin-bottom: 20px;
        }

        .order-details {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .order-details p {
            margin-bottom: 10px;
        }

        .order-details strong {
            font-size: 18px;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }

        .qr-code img {
            width: 250px;
            height: 250px;
        }

        .contact-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .contact-buttons a {
            padding: 10px 20px;
            margin: 5px;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .email-button {
            background-color: #4caf50;
        }

        .whatsapp-button {
            background-color: #25d366;
        }

        .confirm-button {
            padding: 10px 20px;
            background-color: #9a4051;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .confirm-button:hover {
            background-color: #ffe600;
        }

        .thank-you-header {
            width: 100%;
            overflow: hidden; /* Hides the overflow part of the text */
            background-color: #b84c65;
            padding: 5px;
            text-align: center;
        }

        .thank-you-header p {
            display: inline-block;
            font-family: 'Caveat Brush', cursive;
            font-size: 20px;
            color: white;
            white-space: nowrap;
            animation: scroll-text 6s linear infinite;
        }

        /* Animation for scrolling the text from right to left */
        @keyframes scroll-text {
            0% {
                transform: translateX(100%); /* Start from the right */
            }
            100% {
                transform: translateX(-100%); /* End at the left */
            }
        }
        /* Form Styles */
        .customer-details-form {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .customer-details-form h3 {
            font-family: 'Caveat Brush', cursive;
            font-size: 22px;
            color: #b84c65;
            margin-bottom: 15px;
        }

        .customer-details-form label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-top: 10px;
        }

        .customer-details-form input[type="text"],
        .customer-details-form input[type="email"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .customer-details-form input[type="text"]:focus,
        .customer-details-form input[type="email"]:focus {
            border-color: #b84c65;
            outline: none;
        }

        .customer-details-form .confirm-button {
            margin-top: 20px;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="thank-you-header">
    <p>Thank you for buying with us!</p>
</div>

<!-- Order Summary Section -->
<section class="order-summary">
    <h2>Your Order Confirmation</h2>
    <div class="order-details">
        <?php
        // Display cart items for confirmation
        foreach ($_SESSION['cart'] as $cart_item) {
            echo "<p>" . $cart_item['name'] . " - Quantity: " . $cart_item['quantity'] . " - Total Price: RM " . number_format($cart_item['total_price'], 2) . "</p>";
        }
        ?>
        <p><strong>Total: RM <?php echo number_format($total, 2); ?></strong></p>
    </div>

    <!-- QR Code for Payment -->
    <div class="qr-code">
        <h3>Scan this QR Code to Pay:</h3>
        <img src="qrcode.jpg" alt="QR Code for Payment">
    </div>

    <!-- Contact Options -->
    <div class="contact-buttons">
        <h3>Once payment is made, please send receipt as proof by contacting us and we will verify your orders!:</h3>
        <a href="mailto:JucchyDessert@gmail.com" class="email-button">Email Us</a>
        <a href="https://wa.me/0192816121?text=Hi, I want to send receipt." class="whatsapp-button" target="_blank">WhatsApp Us</a>
    </div>

    <!-- Customer Details Form -->
    <form method="POST" action="">
        <h3>Enter Your Details:</h3>
        <label for="customer_name">Name:</label>
        <input type="text" id="customer_name" name="customer_name" required><br><br>

        <label for="customer_phone">Phone Number:</label>
        <input type="text" id="customer_phone" name="customer_phone" required><br><br>

        <label for="customer_email">Email (optional):</label>
        <input type="email" id="customer_email" name="customer_email"><br><br>

        <button type="submit" name="confirm_order" class="confirm-button">Confirm Order</button>
    </form>
</section>

</body>
</html>
