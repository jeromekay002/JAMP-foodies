<?php
session_start();
require_once "../include_front/connect.php";
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($connect, $_POST['username']);
    $password = $_POST['password'];

    // Check if user exists
    $query = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['user_id'];
            $_SESSION['admin_name'] = $user['full_name'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
        }
    } else {
        $_SESSION['error'] = "Admin account not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>

    <!-- ✅ Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            height: 100vh;
            background: url('../images/bg.jpg') no-repeat left center;
            background-size: auto 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-right: 5%;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
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

        button {
            background-color: #ff4757;
            color: white;
        }

        button:hover {
            background-color: #e03c4d;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Admin Login</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <i class='fas fa-exclamation-circle'></i> <?= $_SESSION['error'] ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post" class="login-form" autocomplete="off">
            <div class="mb-3">
                <label for="login-username" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email
                </label>
                <input type="email" id="login-username" name="username" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label for="login-password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <input type="password" id="login-password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" class="btn w-100" style="background-color: #ff4757; color: white;">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </button>
        </form>

        <div class="switch-link">
            Don't have an account? <a href="admin_register.php">Register here</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>