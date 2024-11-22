<?php
session_start();

// Check if the 'index' parameter is set (to know which item to remove)
if (isset($_GET['index']) && is_numeric($_GET['index'])) {
    $index = $_GET['index'];

    // Check if the index exists in the cart
    if (isset($_SESSION['cart'][$index])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$index]);

        // Re-index the array to fix any gaps in the cart array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Redirect back to the menu or cart page
header('Location: menu.php');
exit();
?>
