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

// Update item details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $itemId = $_POST['item_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $image = $_POST['image_1'];

    $sql = "UPDATE menu_items SET name=?, description=?, stock=?, price=?, image_1=? WHERE item_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssidsi", $name, $description, $stock, $price, $image, $itemId);

    if ($stmt->execute()) {
        echo "<script>alert('Item updated successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to update item. Please try again.');</script>";
    }

    $stmt->close();
}

// Fetch item details for editing
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    $sql = "SELECT * FROM menu_items WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<script>alert('Invalid item ID.'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item - Fantasy Dessert Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .edit-container {
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .edit-container h2 {
            font-family: 'Fruktur', serif;
            font-size: 24px;
            color: #b84c65;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        button {
            background-color: #b84c65;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 3px;
        }

        button:hover {
            background-color: #8e4aad;
        }

        .cancel {
            background-color: #666;
            margin-left: 10px;
        }

        .cancel:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Menu Item</h2>
        <form action="edit_menu_item.php" method="post">
            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
            <label for="name">Item Name</label>
            <input type="text" name="name" id="name" value="<?php echo $item['name']; ?>" required>
            <label for="description">Description</label>
            <input type="text" name="description" id="description" value="<?php echo $item['description']; ?>" required>
            <label for="stock">Stock</label>
            <input type="number" name="stock" id="stock" value="<?php echo $item['stock']; ?>" required>
            <label for="price">Price</label>
            <input type="number" name="price" id="price" step="0.01" value="<?php echo $item['price']; ?>" required>
            <label for="image_1">Image Path</label>
            <input type="text" name="image_1" id="image_1" value="<?php echo $item['image_1']; ?>" required>
            <button type="submit">Save Changes</button>
            <a href="admin_dashboard.php"><button type="button" class="cancel">Cancel</button></a>
        </form>
    </div>
</body>
</html>
