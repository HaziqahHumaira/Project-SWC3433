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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the item if the user confirms
    $itemId = $_POST['item_id'];

    $sql = "DELETE FROM menu_items WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);

    if ($stmt->execute()) {
        echo "<script>alert('Item deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to delete item. Please try again.'); window.location.href='admin_dashboard.php';</script>";
    }

    $stmt->close();
} elseif (isset($_GET['id'])) {
    // Show the confirmation screen if item ID is set in URL
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
    <title>Confirm Delete - Fantasy Dessert Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .delete-container {
            max-width: 500px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
            text-align: center;
        }

        .delete-container h2 {
            font-family: 'Fruktur', serif;
            font-size: 24px;
            color: #b84c65;
            margin-bottom: 20px;
        }

        .delete-container p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .delete-container button {
            background-color: #b84c65;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
        }

        .delete-container button:hover {
            background-color: #8e4aad;
        }

        .cancel {
            background-color: #666;
        }

        .cancel:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete the item: <strong><?php echo $item['name']; ?></strong>?</p>
        <form action="delete_menu_item.php" method="post">
            <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
            <button type="submit" name="confirm_delete">Yes, Delete</button>
            <a href="admin_dashboard.php"><button type="button" class="cancel">Cancel</button></a>
        </form>
    </div>
</body>
</html>
