<?php
session_start();
include('../include_front/connect.php');

if (!$connect) {
    $_SESSION['message'] = 'Database connection failed.';
    $_SESSION['message_type'] = 'error';
    header('Location: ../food.php');
    exit;
}

if (isset($_GET['food_id'])) {
    $food_id = intval($_GET['food_id']);

    // Get image file
    $stmt = $connect->prepare("SELECT food_image FROM food WHERE food_id = ?");
    $stmt->bind_param("i", $food_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Delete image file
    if (!empty($image) && file_exists('../../images/food/' . $image)) {
        unlink('../../images/food/' . $image);
    }

    // Delete food from DB
    $delete = $connect->prepare("DELETE FROM food WHERE food_id = ?");
    $delete->bind_param("i", $food_id);

    if ($delete->execute()) {
        $_SESSION['success'] = "Food item deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting food: " . $delete->error;
    }

    $delete->close();
    $connect->close();

    header('Location: ../food.php');
    exit;
} else {
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'error';
    header('Location: ../food.php');
    exit;
}
