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

    // Sanitize and escape inputs
    $food_name = mysqli_real_escape_string($connect, $_POST['food_name']);
    $category = mysqli_real_escape_string($connect, $_POST['category']);
    $price = mysqli_real_escape_string($connect, $_POST['price']);
    $quantity = mysqli_real_escape_string($connect, $_POST['quantity']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $status = mysqli_real_escape_string($connect, $_POST['status']);

    // Handle image upload
    $target_dir = "../../images/food/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] === 0) {
        $image_name = time() . "_" . basename($_FILES['food_image']['name']);
        $target_file = $target_dir . $image_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error'] = "Invalid image type. Only JPG, JPEG, PNG, GIF allowed.";
            header("Location: ../food.php");
            exit();
        }

        if (move_uploaded_file($_FILES['food_image']['tmp_name'], $target_file)) {
            // Insert into database
            $relative_path = $image_name;

            $sql = "INSERT INTO food (food_name, product_category, food_image, price, quantity, description, status)
                    VALUES ('$food_name', '$category', '$relative_path', '$price', '$quantity', '$description', '$status')";

            if (mysqli_query($connect, $sql)) {
                $_SESSION['success'] = "Food added successfully.";
            } else {
                $_SESSION['error'] = "Database error: " . mysqli_error($connect);
            }
        } else {
            $_SESSION['error'] = "Image upload failed.";
        }
    } else {
        $_SESSION['error'] = "No image uploaded.";
    }

    header("Location: ../food.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../food.php");
    exit();
}
