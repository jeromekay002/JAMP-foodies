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

    $category_id   = mysqli_real_escape_string($connect, $_POST['category_id']);
    $category_name = mysqli_real_escape_string($connect, $_POST['category_name']);
    $description   = mysqli_real_escape_string($connect, $_POST['description']);
    $status        = mysqli_real_escape_string($connect, $_POST['status']);

    // 1. get the current image from the database 
    $old_image_sql = "SELECT category_image FROM category WHERE category_id ='$category_id'";
    $old_image_res = mysqli_query($connect, $old_image_sql);
    $old_image_row = mysqli_fetch_assoc($old_image_res);
    $old_image = $old_image_row['category_image'];

    // 2. chech if a new image is uploaded
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $target_dir = "../../images/categories/";

        // create the folder if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = time() . "_" . basename($_FILES["category_image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Validate image type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['error'] = "Invalid image type. Only JPG, JPEG, PNG, GIF allowed.";
            header("Location: ../category.php");
            exit();
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
            // Optional: delete old image if exists
            if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                unlink($target_dir . $old_image);
            }
            $final_image = $image_name;
        } else {
            $_SESSION['error'] = "Error uploading new image.";
            header("Location: ../category.php");
            exit();
        }
    } else {
        // If no new image is uploaded, keep the old image
        $final_image = $old_image;
    }

    // 3. update the category in the database
    $sql_update = "UPDATE category
                    SET category_name = '$category_name',
                        description = '$description',
                        status = '$status',
                        category_image = '$final_image'
                   WHERE category_id = '$category_id'";
    if (mysqli_query($connect, $sql_update)) {
        $_SESSION['success'] = "Category updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating category: " . mysqli_error($connect);
    }

    header("Location: ../category.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../category.php");
    exit();
}
