<?php
session_start();
require_once "../include_front/connect.php";
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<script>console.log('Form submitted');</script>";
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";

    $food_id     = mysqli_real_escape_string($connect, $_POST['food_id']);
    $food_name   = mysqli_real_escape_string($connect, $_POST['food_name']);
    $category    = mysqli_real_escape_string($connect, $_POST['category']);
    $price       = mysqli_real_escape_string($connect, $_POST['price']);
    $quantity    = mysqli_real_escape_string($connect, $_POST['quantity']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $status      = mysqli_real_escape_string($connect, $_POST['status']);

    // 1. Get current image from DB
    $old_img_sql = "SELECT food_image FROM food WHERE food_id = '$food_id'";
    $old_img_res = mysqli_query($connect, $old_img_sql);
    $old_img_row = mysqli_fetch_assoc($old_img_res);
    $old_image = $old_img_row['food_image'];

    $target_dir = "../../images/food/";

    // Create folder if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 2. Check if a new image was uploaded
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] === 0) {
        $image_name = time() . "_" . basename($_FILES['food_image']['name']);
        $target_file = $target_dir . $image_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error'] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: ../food.php");
            exit();
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $target_file)) {
            // Delete old image if exists
            if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                unlink($target_dir . $old_image);
            }
            $final_image = $image_name;
        } else {
            $_SESSION['error'] = "Failed to upload new image.";
            header("Location: ../food.php");
            exit();
        }
    } else {
        // No new image — keep old
        $final_image = $old_image;
    }

    // 3. Update food record
    $sql = "UPDATE food SET
                food_name = '$food_name',
                product_category = '$category',
                food_image = '$final_image',
                price = '$price',
                quantity = '$quantity',
                description = '$description',
                status = '$status'
            WHERE food_id = '$food_id'";

    if (mysqli_query($connect, $sql)) {
        $_SESSION['success'] = "Food item updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating food: " . mysqli_error($connect);
    }

    header("Location: ../food.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../food.php");
    exit();
}
