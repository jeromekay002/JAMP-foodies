<?php
session_start();
include("include_front/connect.php");
if (!$connect) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['food_id']) && isset($_POST['food_name']) && isset($_POST['food_image']) && isset($_POST['price'])) {
    $food_id = $_POST['food_id'];
    $food_name = $_POST['food_name'];
    $food_image = $_POST['food_image'];
    $food_price = $_POST['price'];
    $quantity = 1;

    // if customer is logged in
    if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];

        // 1. Check if user has an active shopping cart
        $cart_check_sql = "SELECT cart_id FROM shoppingcart WHERE customer_id='$customer_id'";
        $cart_check_result = mysqli_query($connect, $cart_check_sql);
        if (!$cart_check_result) {
            die("Cart check Error: " . mysqli_error($connect));
        }

        if (mysqli_num_rows($cart_check_result) > 0) {
            $cart_check_row = mysqli_fetch_assoc($cart_check_result);
            $cart_id = $cart_check_row['cart_id'];
        } else {
            // Create a new cart for a new customer
            $create_new_cart_sql = "INSERT INTO shoppingcart (customer_id) VALUES ('$customer_id')";
            $create_new_cart_result = mysqli_query($connect, $create_new_cart_sql);
            if (!$create_new_cart_result) {
                die("Create new cart error: " . mysqli_error($connect));
            }
            $cart_id = mysqli_insert_id($connect);
        }

        // 2. Check if food item already exists in the cart
        $cart_item_check_sql = "SELECT * FROM cartitems WHERE cart_id='$cart_id' AND food_id='$food_id'";
        $cart_item_check_result = mysqli_query($connect, $cart_item_check_sql);
        if (!$cart_item_check_result) {
            die("Cart Item Check error: " . mysqli_error($connect));
        }

        if (mysqli_num_rows($cart_item_check_result) > 0) {
            // Update the quantity and price of the existing cart item
            $update_cart_item_sql = "UPDATE cartitems SET quantity = quantity + $quantity, price='$food_price' WHERE cart_id='$cart_id' AND food_id='$food_id'";
            $update_cart_item_result = mysqli_query($connect, $update_cart_item_sql);
            if (!$update_cart_item_result) {
                die("Update Cart error: " . mysqli_error($connect));
            }

            $updated_at = date("Y-m-d H:i:s");
            $update_shopping_cart_sql = "UPDATE shoppingcart SET updated_at ='$updated_at'";
            $update_shopping_cart_result = mysqli_query($connect, $update_shopping_cart_sql);
            if(!$update_shopping_cart_result){
                die("Update Shopping cart error:" . mysqli_error($connect));
            }
        } else {
            // Insert a new cart item
            $insert_cart_item_sql = "INSERT INTO cartitems (cart_id, food_id, food_name, food_image, quantity, price) VALUES ('$cart_id', '$food_id', '$food_name', '$food_image', '$quantity', '$food_price')";
            $insert_cart_item_result = mysqli_query($connect, $insert_cart_item_sql);
            if (!$insert_cart_item_result) {
                die("Insert cart items Error: " . mysqli_error($connect));
            }
        }

        $_SESSION['success'] = "Item added to cart successfully.";
        header("Location: index.php");
        exit();
    } else {
        // Customer not logged in, manage cart using session
        $cart_item = array(
            'id' => $food_id,
            'name' => $food_name,
            'price' => $food_price,
            'quantity' => $quantity,
            'image' => $food_image
        );

        if (isset($_SESSION['cart'])) {
            $item_found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $food_id) {
                    $item['quantity'] += $quantity;
                    $item_found = true;
                    break;
                }
            }
            if (!$item_found) {
                $_SESSION['cart'][] = $cart_item;
            }
        } else {
            $_SESSION['cart'] = array($cart_item);
        }

        $_SESSION['success'] = "Item added to cart successfully.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request";
    header("Location: index.php");
    exit();
}
?>
