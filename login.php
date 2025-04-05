<?php
session_start();
include("include_front/connect.php");
if(!$connect){
    die("Connection failed: " . mysqli_connect_error());
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];

    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "<div class='alert alert-danger' role='alert'>Invalid email format.</div>";
    }

    if(empty($errors)){
        // Prepare select statement
        $verify_user_sql = "SELECT user_id, password FROM users WHERE email=?";
        $stmt = mysqli_prepare($connect, $verify_user_sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $verify_user_result = mysqli_stmt_get_result($stmt);

        if($verify_user_result && mysqli_num_rows($verify_user_result) > 0){
            $verify_user_row = mysqli_fetch_assoc($verify_user_result);

            // Verify password
            if(password_verify($password, $verify_user_row['password'])){
                // Fetch customer details
                $customer_get_details_sql = "SELECT customer_id FROM customers WHERE user_id=?";
                $stmt_customer = mysqli_prepare($connect, $customer_get_details_sql);
                mysqli_stmt_bind_param($stmt_customer, "i", $verify_user_row['user_id']);
                mysqli_stmt_execute($stmt_customer);
                $customer_get_details_result = mysqli_stmt_get_result($stmt_customer);
                
                if($customer_get_details_result && mysqli_num_rows($customer_get_details_result) == 1){
                    $customer_get_details_row = mysqli_fetch_assoc($customer_get_details_result);
                    $_SESSION['customer_id'] = $customer_get_details_row['customer_id'];

                    // Handle cart items if exist
                    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
                        $customer_id = $_SESSION['customer_id'];

                        // Check if user has an active cart
                        $cart_check_sql = "SELECT cart_id FROM shoppingcart WHERE customer_id=?";
                        $stmt_cart = mysqli_prepare($connect, $cart_check_sql);
                        mysqli_stmt_bind_param($stmt_cart, "i", $customer_id);
                        mysqli_stmt_execute($stmt_cart);
                        $cart_check_result = mysqli_stmt_get_result($stmt_cart);

                        if($cart_check_result){
                            if(mysqli_num_rows($cart_check_result) > 0){
                                // Get existing cart ID
                                $cart_check_row = mysqli_fetch_assoc($cart_check_result);
                                $cart_id = $cart_check_row['cart_id'];
                            } else {
                                // Create a new cart
                                $create_new_cart_sql = "INSERT INTO shoppingcart (customer_id) VALUES (?)";
                                $stmt_new_cart = mysqli_prepare($connect, $create_new_cart_sql);
                                mysqli_stmt_bind_param($stmt_new_cart, "i", $customer_id);
                                mysqli_stmt_execute($stmt_new_cart);
                                $cart_id = mysqli_insert_id($connect);   
                            }

                            foreach($_SESSION['cart'] as $cart_item){
                                $food_id = $cart_item['id'];
                                $food_name = $cart_item['name'];
                                $food_image = $cart_item['image'];
                                $food_price = $cart_item['price'];
                                $quantity = $cart_item['quantity'];
                                $total_price = $food_price * $quantity;

                                // Check if item already exists in the cart
                                $cart_item_check_sql = "SELECT * FROM cartitems WHERE cart_id=? AND food_id=?";
                                $stmt_cart_item = mysqli_prepare($connect, $cart_item_check_sql);
                                mysqli_stmt_bind_param($stmt_cart_item, "ii", $cart_id, $food_id);
                                mysqli_stmt_execute($stmt_cart_item);
                                $cart_item_check_result = mysqli_stmt_get_result($stmt_cart_item);

                                if($cart_item_check_result){
                                    if(mysqli_num_rows($cart_item_check_result) > 0){
                                        // Update existing cart item
                                        $update_cart_item_sql = "UPDATE cartitems SET quantity = quantity + ?, price=?, total_price=quantity * ? WHERE cart_id=? AND food_id=?";
                                        $stmt_update_cart = mysqli_prepare($connect, $update_cart_item_sql);
                                        mysqli_stmt_bind_param($stmt_update_cart, "idiis", $quantity, $food_price, $food_price, $cart_id, $food_id);
                                        mysqli_stmt_execute($stmt_update_cart);
                                    } else {
                                        // Insert new cart item
                                        $insert_cart_item_sql = "INSERT INTO cartitems(cart_id, food_id, food_name, food_image, quantity, price, total_price) VALUES(?, ?, ?, ?, ?, ?, ?)";
                                        $stmt_insert_cart = mysqli_prepare($connect, $insert_cart_item_sql);
                                        mysqli_stmt_bind_param($stmt_insert_cart, 'iissidi', $cart_id, $food_id, $food_name, $food_image, $quantity, $food_price, $total_price);
                                        mysqli_stmt_execute($stmt_insert_cart);
                                    }
                                }
                            }
                            unset($_SESSION['cart']);
                        }
                    }

                    if(isset($_POST['checkoutLogin'])){
                        $_SESSION['success'] = "Logged in Successfully";
                        header("Location: checkout.php");
                        exit();
                    } else if(isset($_POST['submit'])){
                        $_SESSION['success'] = "Logged in Successfully";
                        header("Location: index.php");
                        exit();
                    }
                }
            } else {
                $errors['password'] = "<div class='alert alert-danger' role='alert'>Incorrect Password</div>";
            }
        } else {
            $errors['email'] = "<div class='alert alert-danger' role='alert'>User not found</div>";
        }
    }

    $_SESSION['login_errors'] = $errors;
    $_SESSION['old_input'] = ['email' => $email];
    header("Location: index.php#loginSection");
    exit();
}
?>
