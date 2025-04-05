<?php
    session_start();
    include("include_front/connect.php");
    if(!$connect){
        die("Connection Failed: " . mysqli_connect_error());
    }
    if(isset($_GET['food_id'])){
        $food_id = $_GET['food_id'];

        if(isset($_SESSION['customer_id'])){
            // user is logged in
            $customer_id = $_SESSION['customer_id'];
            $delete_food_items_sql = "DELETE FROM cartitems WHERE food_id = ? AND cart_id = (SELECT cart_id FROM shoppingcart WHERE customer_id = ?)";

            if($stmt = mysqli_prepare($connect, $delete_food_items_sql)){
                mysqli_stmt_bind_param($stmt, 'ii', $food_id, $customer_id);
                if(mysqli_stmt_execute($stmt)){
                    $_SESSION['success'] = "Item successfully deleted from cart";
                }else{
                    die("Delete L error: " . mysqli_error($connect));
                }
            }else{
                die("Preparing Delete error: " . mysqli_error($connect));
            }
        }else{
            // no user logged in
            if(isset($_SESSION['cart'])){
                foreach($_SESSION['cart'] as $key => $item){
                    if($item['id'] == $food_id){
                        unset($_SESSION['cart'][$key]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                        $_SESSION['success'] = "Item successfully deleted from cart.";
                        break;
                    }
                }
            }
        }
        header("Location: index.php");
        exit();
    }else{
        die("Invalid request");
    }
?>