<?php
session_start();

// Check if the 'index' and 'quantity' parameters are set
if (isset($_GET['index']) && is_numeric($_GET['index']) && isset($_POST['quantity'])) {
    $index = $_GET['index'];
    $quantity = intval($_POST['quantity']);

    // Check if the index exists in the cart and the quantity is valid
    if (isset($_SESSION['cart'][$index]) && $quantity > 0) {
        $cart_item = &$_SESSION['cart'][$index];

        // Update the quantity and total price
        $cart_item['quantity'] = $quantity;
        $cart_item['total_price'] = $cart_item['quantity'] * $cart_item['price'];
    }
}

// Redirect back to the cart or menu page
header('Location: menu.php');
exit();
?>
