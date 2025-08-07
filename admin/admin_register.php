<?php
session_start();
require_once "../include_front/connect.php";
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($connect, $_POST['full_name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = $_POST['password'];
    $role = 'admin';

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for existing email
    $check_sql = "SELECT * FROM users WHERE email = '$email'";
    $check_res = mysqli_query($connect, $check_sql);

    if (mysqli_num_rows($check_res) > 0) {
        $_SESSION['error'] = "Email already exists.";
        header("Location: admin_register.php");
        exit();
    }

    $sql = "INSERT INTO users (full_name, email, role, password)
            VALUES ('$full_name', '$email', '$role', '$hashed_password')";

    if (mysqli_query($connect, $sql)) {
        $_SESSION['success'] = "Account created successfully. Please login.";
        header("Location: admin_login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed: " . mysqli_error($connect);
        header("Location: admin_register.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Admin Account</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Reuse the same styles as login */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            height: 100vh;
            background: url('bg.jpg') no-repeat left center;
            background-size: auto 100%;
            background-image: url(../images/bg.jpg);
            display: flex;
            justify-content: center;
            align-items: center;
            padding-right: 5%;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
            text-wrap: nowrap;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            background-color: #ff4757;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #e03c4d;
        }

        .switch-link {
            text-align: center;
            margin-top: 15px;
        }

        .switch-link a {
            color: #ff4757;
            text-decoration: none;
            font-weight: bold;
        }

        .switch-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
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
    <div class="form-container">
        <h2>Create Admin Account</h2>

        <form method="post" action="admin_register.php" class="register-form">
            <div class="mb-3">
                <label for="full_name" class="form-label">
                    <i class="fas fa-user me-2"></i>Full Name
                </label>
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Enter your name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email
                </label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
            </div>

            <button type="submit" class="btn" style="background-color: #ff4757; color: white;">
                <i class="fas fa-user-plus me-1"></i> Create Account
            </button>
        </form>
        <div class="switch-link">
            Already have an account? <a href="admin_login.php">Login here</a>
        </div>
    </div>

    <!-- ✅ Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>