<?php
session_start();
require_once "include_front/connect.php";
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<script>console.log('Form submitted');</script>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Start transaction
    mysqli_begin_transaction($connect);

    // Get customer details
    $customer_id = $_POST['customer-id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['delivery_address'];
    $phone = $_POST['phone_number'];
    $payment_method = $_POST['payment_method'];
    $total_cost = $_POST['total_cost'];
    $vat_amount = $_POST['vat_amount'];
    $total_amount = $_POST['total_amount'];
    $now = date('Y-m-d H:i:s');


    try {
        // 1. UPDATE customers table

        // Update existing customer record
        $update_customer = "UPDATE customers SET full_name = ?, email = ?, phone_number = ?, delivery_address = ? WHERE customer_id = ?";
        $stmt_upd = mysqli_prepare($connect, $update_customer);
        mysqli_stmt_bind_param($stmt_upd, 'ssssi', $full_name, $email, $phone, $address, $customer_id);
        mysqli_stmt_execute($stmt_upd);
        mysqli_stmt_close($stmt_upd);


        // 2. UPDATE users table
        $get_user = mysqli_prepare($connect, "SELECT user_id FROM customers WHERE customer_id = ?");
        mysqli_stmt_bind_param($get_user, 'i', $customer_id);
        mysqli_stmt_execute($get_user);
        mysqli_stmt_bind_result($get_user, $user_id);
        mysqli_stmt_fetch($get_user);
        mysqli_stmt_close($get_user);

        if (!empty($user_id)) {
            $update_user = "UPDATE users SET full_name = ?, email = ? WHERE user_id = ?";
            $stmt_user = mysqli_prepare($connect, $update_user);
            mysqli_stmt_bind_param($stmt_user, 'ssi', $full_name, $email, $user_id);
            mysqli_stmt_execute($stmt_user);
            mysqli_stmt_close($stmt_user);
        }

        // 3. Insert into orders table
        $insert_order = "INSERT INTO orders (customer_id, phone_number, delivery_address, total_amount, payment_method, payment_status, order_status, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, 'Pending', 'Received', ?, ?)";
        $stmt_order = mysqli_prepare($connect, $insert_order);
        mysqli_stmt_bind_param($stmt_order, 'issdsss', $customer_id, $phone, $address, $total_amount, $payment_method, $now, $now);
        mysqli_stmt_execute($stmt_order);
        $order_id = mysqli_insert_id($connect);
        mysqli_stmt_close($stmt_order);

        // 4. Insert order items
        foreach ($_POST['food_id'] as $index => $food_id) {
            $food_name = $_POST['food_name'][$index];
            $quantity = $_POST['quantity'][$index];
            $price = $_POST['food_price'][$index];
            $total_price = $_POST['total_price'][$index];

            // Get food image
            $stmt_img = mysqli_prepare($connect, "SELECT food_image FROM food WHERE food_id = ?");
            mysqli_stmt_bind_param($stmt_img, 'i', $food_id);
            mysqli_stmt_execute($stmt_img);
            mysqli_stmt_bind_result($stmt_img, $food_image);
            mysqli_stmt_fetch($stmt_img);
            mysqli_stmt_close($stmt_img);

            $stmt_item = mysqli_prepare($connect, "INSERT INTO orderitems (order_id, food_id, food_name, food_image, quantity, price, total_price) 
                                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_item, 'iissidd', $order_id, $food_id, $food_name, $food_image, $quantity, $price, $total_price);
            mysqli_stmt_execute($stmt_item);
            mysqli_stmt_close($stmt_item);
        }

        // Commit transaction
        mysqli_commit($connect);

        // Clear cart session if needed
        unset($_SESSION['cart']);

        $_SESSION['success_message'] = "Your order has been placed successfully!";
        $_SESSION['order_id'] = $order_id;
        $_SESSION['customer_name'] = $full_name;
        header("Location: order_success.php"); // Redirect to success page
        exit();
    } catch (Exception $e) {
        mysqli_rollback($connect);
        $_SESSION['error_message'] = "Order failed: " . $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: checkout.php");
    exit();
}
