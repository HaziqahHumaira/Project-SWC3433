<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "syakila03"; // Your MySQL root password
$dbname = "admins";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['name'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    // If item already exists in cart, update quantity
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['item_id'] == $item_id) {
            $item['quantity'] += $quantity;
            $item['total_price'] += $quantity * $price;
            $found = true;
            break;
        }
    }

    // If item not found, add new item to cart
    if (!$found) {
        $_SESSION['cart'][] = [
            'item_id' => $item_id,
            'name' => $name,
            'quantity' => $quantity,
            'price' => $price,
            'total_price' => $quantity * $price
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantasy Dessert Shop - Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Arvo:wght@400;700&family=Fruktur:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Caveat+Brush&display=swap" rel="stylesheet">
    <style>
/* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    height: 100%; /* Make sure the body and html take the full height */
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f9f9f9;
    display: flex; /* Enables flexbox */
    flex-direction: column; /* Stack elements vertically */
}

/* Header Styles */
.header {
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Fruktur' , cursive;
    padding: 10px 20px; /* Shorter header height */
    background-color: #b84c65; /* Customizable background color */
    height: 60px; /* Shortened height */
    margin-bottom: 10px; /* Reduces gap between header and hero section */
}

.logo img {
    width: 90px; /* Adjust the logo size */
}

.navbar {
    flex-grow: 1;
    display: flex;
    justify-content: center;
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 60px;
}

.navbar ul li a {
    text-decoration: none;
    color: #ffe600;
    font-size: 20px;
}

.navbar ul li a:hover {
    color: #ff7f50;
}


        /* Menu Section */
        .menu {
            margin-top: 30px;
        }

        .menu h2 {
            text-align: center;
            font-family: 'Fruktur', serif;
            font-size: 28px;
            color: #b84c65;
            margin-bottom: 20px;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .category {
            background-color: #fff;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: calc(33.333% - 20px);
            box-sizing: border-box;
        }

        .category img {
            width: 100%;
            height: auto;
            max-height: 350px;
            object-fit: cover;
            border-radius: 5px;
        }

        .category h3 {
            font-family: 'Arvo', serif;
            color: #333;
            font-size: 18px;
            margin-top: 10px;
        }

        .category p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .category form {
            margin-top: 10px;
        }

        .category label {
            font-size: 14px;
            margin-right: 5px;
        }

        .category select {
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        .category button {
            background-color: #b84c65;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .category button:hover {
            background-color: #8e4aad;
        }

/* Cart Summary Section */
.cart-summary {
    position: fixed;       /* Fix the summary in place */
    top: 20px;             /* Set distance from the top of the page */
    right: 20px;           /* Set distance from the right edge of the page */
    width: 300px;          /* Set width of the cart summary */
    padding: 20px;
    background-color: #b84c65;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 999;          /* Ensure it stays above other content */
    max-height: 80%;       /* Optional: limits the height to avoid taking up too much space */
    overflow-y: auto;      /* Scrollable content if it exceeds the max-height */
}

.cart-summary h2 {
    font-family: 'Caveat Brush', cursive;
    font-size: 24px;
    color: #fff;
    margin-bottom: 15px;
}

.cart-items p {
    font-size: 16px;
    margin-bottom: 10px;
}

.cart-items strong {
    font-size: 18px;
}

.cart-item {
    margin-bottom: 10px;
}

.remove-item,
.edit-item {
    color: #ffe600;
    text-decoration: none;
    font-size: 14px;
}

.remove-item:hover,
.edit-item:hover {
    text-decoration: underline;
}

/* Checkout Button (aligned to the right) */
.checkout-container {
    text-align: right;
    margin-top: 20px;
}

.checkout-button {
    padding: 10px 20px;
    background-color: #ffe600;
    color: black;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
}

.checkout-button:hover {
    background-color: #9a4051;
}


    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="Project.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="gallery.html">Gallery</a></li>
                <li><a href="faq.html">Faq</a></li>
            </ul>
        </nav>
    </header>

    <!-- Menu Section -->
    <section class="menu">
        <h2>Our Dessert Categories</h2>
        <div class="categories">
            <?php
            // Fetch menu items from the database
            $sql = "SELECT * FROM menu_items";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="category">
                        <div class="image-slider">
                            <img src="<?php echo $row['image_1']; ?>" alt="<?php echo $row['name']; ?> Image" class="slider-image">
                        </div>
                        <h3><?php echo $row['name']; ?></h3>
                        <p>
                            <?php echo $row['description']; ?>
                            <?php
                            if ($row['name'] === 'Iced Coffee' || $row['name'] === 'Lemonade' || $row['name'] === 'Matcha Latte' || $row['name'] === 'Hot Chocolate' || $row['name'] === 'Chai Tea' || $row['name'] === 'Mango Smoothie' || $row['name'] === 'Berry Blast Smoothie' || $row['name'] === 'Iced Lemon Tea') {
                                echo "<br>Size: Regular";
                            } else {
                                echo "<br>Available Sizes: Small, Medium, Large";
                            }
                            ?>
                        </p>
                        <p>Price: RM <?php echo number_format($row['price'], 2); ?></p>
                        
                        <!-- Add to Cart Form -->
                        <form method="POST">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                            <label for="quantity">Quantity:</label>
                            <select name="quantity" id="quantity">
                                <option value="1">1 pieces</option>
                                <option value="6">6 pieces / NotForDrinks</option>
                                <option value="12">12 pieces / NotForDrinks</option>
                            </select>
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No items found in the menu.</p>";
            }
            ?>
        </div>
    </section>

<!-- Cart Summary Section -->
<section class="cart-summary">
    <h2>Cart Summary</h2>
    <div class="cart-items">
        <?php
        if (!empty($_SESSION['cart'])) {
            $total = 0;
            foreach ($_SESSION['cart'] as $index => $cart_item) {
                echo "<div class='cart-item'>";
                echo "<p>" . $cart_item['name'] . " - Quantity: " . $cart_item['quantity'] . " - Total Price: RM " . number_format($cart_item['total_price'], 2) . "</p>";
                
                // Remove button
                echo "<a href='remove_item.php?index=$index' class='remove-item'>Remove</a> | ";
                
                // Edit button (opens a form to edit quantity)
                echo "<a href='#' class='edit-item' onclick='editItem($index)'>Edit</a>";
                echo "</div>";
                $total += $cart_item['total_price'];
            }
            echo "<p><strong>Total: RM " . number_format($total, 2) . "</strong></p>";
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
    </div>
    <!-- Checkout Button aligned to the right -->
    <div class="checkout-container">
        <a href="checkout.php" class="checkout-button">Checkout</a>
    </div>
</section>

<script>
// Function to handle the edit action
function editItem(index) {
    // Prompt user for new quantity
    var newQuantity = prompt("Enter new quantity:");

    if (newQuantity && !isNaN(newQuantity) && newQuantity > 0) {
        // Create a form dynamically to submit the new quantity
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "edit_item.php?index=" + index;

        // Create the quantity input
        var quantityInput = document.createElement("input");
        quantityInput.type = "hidden";
        quantityInput.name = "quantity";
        quantityInput.value = newQuantity;

        form.appendChild(quantityInput);

        // Submit the form
        document.body.appendChild(form);
        form.submit();
    } else {
        alert("Invalid quantity.");
    }
}
</script>

</section>
</body>
</html>

<?php
$conn->close();
?>