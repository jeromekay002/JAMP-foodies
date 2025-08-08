<?php
// my_account.php
session_start();
include("include_front/connect.php");
if(!$connect){
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php#loginSection");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch customer info
$query = "
    SELECT c.*, u.email
    FROM customers c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.customer_id = ?
";
$stmt = $connect->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo "<div class='alert alert-danger'>Customer information not found.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
        }

        .account-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #c82333;
            color: white;
        }

        .section-title {
            margin-top: 30px;
            font-size: 1.2rem;
            color: #495057;
            font-weight: 500;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .info-item {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="account-container">
        <div class="account-header mb-4">
            <h3>👤 My Account</h3>
            <a href="index.php" class="logout-btn">Go back</a>
        </div>

        <div>
            <div class="section-title">Account Details</div>
            <p class="info-item"><strong>Full Name:</strong> <?= htmlspecialchars($customer['full_name']) ?></p>
            <p class="info-item"><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
            <p class="info-item"><strong>Phone Number:</strong> <?= htmlspecialchars($customer['phone_number']) ?></p>
            <p class="info-item"><strong>Address:</strong> <?= htmlspecialchars($customer['delivery_address']) ?></p>
        </div>

        <div class="mt-4">
            <a href="my_orders.php" class="btn btn-outline-primary">📦 View My Orders</a>
        </div>
    </div>
</body>

</html>