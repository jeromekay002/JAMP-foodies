<?php
session_start();
include("include_front/connect.php");

if (!$connect) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    $is_logged_in = isset($_SESSION['customer_id']);
    $update_at = date('Y-m-d H:i:s');

    // Handling the cart update for logged-in users
    if ($is_logged_in) {
        $customer_id = $_SESSION['customer_id'];
        $cart_id = $_POST['cart_id'];
        $quantities = $_POST['quantity'];
        $total_prices = $_POST['total_price'];

        foreach ($quantities as $cart_item_id => $quantity) {
            $total_price = $total_prices[$cart_item_id];
            $quantity = intval($quantity);
            $total_price = floatval($total_price);

            $update_cart_items_sql = "UPDATE cartitems SET quantity = ?, total_price = ? WHERE cart_item_id = ?";
            $stmt = $connect->prepare($update_cart_items_sql);
            $stmt->bind_param('idi', $quantity, $total_price, $cart_item_id);
            $stmt->execute();
            if ($stmt->errno) {
                die('Execute failed: ' . $stmt->error);
            }
            $stmt->close();
        }

        $update_shopping_cart_sql = "UPDATE shoppingcart SET updated_at='$update_at' WHERE cart_id='$cart_id'";
        $update_shopping_cart_result = mysqli_query($connect, $update_shopping_cart_sql);
        if (!$update_shopping_cart_result) {
            die("Update Shopping cart Error: " . mysqli_error($connect));
        }
    } else {
        // Handling the cart update for non-logged-in users
        $cart_items = $_SESSION['cart'];
        $quantities = $_POST['quantity'];
        $total_prices = $_POST['total_price'];

        foreach ($cart_items as &$item) {
            $cart_item_id = $item['id'];
            if (isset($quantities[$cart_item_id])) {
                $item['quantity'] = intval($quantities[$cart_item_id]);
                $item['total_price'] = floatval($total_prices[$cart_item_id]);
            }
        }
        $_SESSION['cart'] = $cart_items;
    }

    // Redirect based on the button clicked
    if (isset($_POST['submit'])) {
        header("Location: checkout.php");
        exit();
    } elseif (isset($_POST['update'])) {
        header("Location: index.php"); 
        exit();
    }
} else {
    echo "Form not submitted";
}
?>
