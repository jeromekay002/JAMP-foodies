<?php
session_start();
// Simulating data – Replace with actual session/order variables
$order_id = $_SESSION['order_id'] ?? 'ORD123456';
$customer_name = $_SESSION['customer_name'] ?? 'Valued Customer';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .success-container {
            max-width: 600px;
            margin: 80px auto;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 60px;
            color: #28a745;
        }

        .order-id {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
        }

        .btn-track {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="success-container">
        <div class="success-icon">
            ✅
        </div>
        <h2 class="mt-3">Thank You, <?= htmlspecialchars($customer_name) ?>!</h2>
        <p>Your order has been placed successfully.</p>
        <p class="order-id">Order ID: <?= htmlspecialchars($order_id) ?></p>

        <a href="track-order.php?order_id=<?= urlencode($order_id) ?>" class="btn btn-primary btn-track">
            Track Your Order
        </a>
        <br>
        <a href="index.php" class="btn btn-link mt-3">Back to Home</a>
    </div>

</body>

</html>