<?php
session_start();
require_once "../include_front/connect.php";
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// adding data to category table
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<script>console.log('Form submitted');</script>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $category_name = mysqli_real_escape_string($connect, $_POST['category_name']);
    $description   = mysqli_real_escape_string($connect, $_POST['description']);
    $status        = mysqli_real_escape_string($connect, $_POST['status']);


    //    handle the image upload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $target_dir = "../../images/categories/";

        // create the folder is it doesnt exist
        if (!is_dir($targer_dir)) {
            mkdir($targer_dir, 0777, true);
        }

        $image_name = time() . "_" . basename($_FILES["category_image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Check file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
                // Insert into database
                $sql = "INSERT INTO category (category_name, category_image, description, status) 
                        VALUES ('$category_name', '$image_name', '$description', '$status')";

                if (mysqli_query($connect, $sql)) {
                    $_SESSION['success'] = "Category added successfully.";
                    header("Location: ../category.php"); // Redirect to categories page
                    exit();
                } else {
                    $_SESSION['error'] = "Error inserting category: " . mysqli_error($connect);
                }
            } else {
                $_SESSION['error'] = "Error uploading image.";
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    } else {
        $_SESSION['error'] = "Image file is required.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: ../category.php");
exit();
