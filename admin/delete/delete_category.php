<?php
session_start();
require_once "../include_front/connect.php";

if (!$connect) {
    $_SESSION['error'] = "Database connection failed.";
    header("Location: ../category.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);

    // Get image filename
    $stmt = $connect->prepare("SELECT category_image FROM category WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Delete image file if it exists
    if (!empty($image) && file_exists("../../images/categories/" . $image)) {
        unlink("../../images/categories/" . $image);
    }

    // Delete from database
    $delete = $connect->prepare("DELETE FROM category WHERE category_id = ?");
    $delete->bind_param("i", $category_id);

    if ($delete->execute()) {
        $_SESSION['success'] = "Category deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete category.";
    }

    $delete->close();
    $connect->close();
    header("Location: ../category.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../category.php");
    exit;
}
