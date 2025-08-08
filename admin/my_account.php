<?php 
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include("include_front/connect.php");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$admin_id = $_SESSION['admin_id'];

$query = $connect->prepare("SELECT * FROM users WHERE user_id = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --theme-color: #ff4757;
        }

        body {
            background-image: url('../images/bg.jpg');
            background-position: center;
            background-size: cover;
            height: 100vh;

            font-family: 'Segoe UI', sans-serif;
        }

        .admin-header {
            background-color: var(--theme-color);
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .account-card {
            background: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            margin-top: 40px;
        }

        .account-card h4 {
            color: var(--theme-color);
            margin-bottom: 25px;
        }

        .account-icon {
            font-size: 2rem;
            color: var(--theme-color);
        }

        .back-btn {
            background-color: var(--theme-color);
            border: none;
        }

        .back-btn:hover {
            background-color: #e84150;
        }

        th {
            width: 30%;
        }
    </style>
</head>

<body>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card account-card">
                    <?php if ($admin): ?>
                        <h4><i class="bi bi-person-fill"></i> Account Details</h4>
                        <table class="table table-borderless">
                            <tr>
                                <th><i class="bi bi-person"></i> Username:</th>
                                <td><?= htmlspecialchars($admin['full_name']) ?></td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-envelope"></i> Email:</th>
                                <td><?= htmlspecialchars($admin['email']) ?></td>
                            </tr>
                            <tr>
                                <th><i class="bi bi-calendar"></i> Joined On:</th>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($admin['created_at']))) ?></td>
                            </tr>
                        </table>
                        <a href="index.php" class="btn back-btn text-white mt-3">
                            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">Admin details not found.</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
