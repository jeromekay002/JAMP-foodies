<?php
session_start();
include("connect.php");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JAMP Foodies</title>

    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/admin.css">
    <!-- bootstrap 5 link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous" />

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <!-- navbar section start -->
    <section class="container" style="border-bottom: 1px solid #0000005b;">
        <nav class="navbar navbar-expand-lg navbar-light" style="min-width: 100%;">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <h3>Jamp F<span class="logo-letters">oo</span>dies</h3>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav active">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="food.php">Food</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php">Category</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="btn create">Logout</button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>
    <!-- navbar section end -->

    <?php
    // Show success message
    if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <i class='fas fa-check-circle'></i> " . $_SESSION['success'] . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
        unset($_SESSION['success']); // Clear message after displaying
    }

    // Show error message
    if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <i class='fas fa-exclamation-circle'></i> " . $_SESSION['error'] . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
        unset($_SESSION['error']); // Clear message after displaying
    }
    ?>