<?php
session_start();
include("include_front/connect.php");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['order_id'])) {
    echo "Order ID missing!";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order info
$order_query = $connect->prepare("SELECT * FROM orders WHERE order_id = ? AND customer_id = ?");
$order_query->bind_param("ii", $order_id, $customer_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows == 0) {
    echo "No such order found!";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch order items
$items_query = $connect->prepare("SELECT * FROM orderitems WHERE order_id = ?");
$items_query->bind_param("i", $order_id);
$items_query->execute();
$items_result = $items_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <style>
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            padding: 30px;
            font-family: 'Segoe UI', sans-serif;
        }

        .order-container {
            background: white;
            max-width: 800px;
            margin: auto;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #444;
        }

        .info,
        .items {
            margin-top: 20px;
        }

        .info p {
            margin: 5px 0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .total {
            text-align: right;
            font-weight: bold;
            color: #333;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="order-container">
        <h2>Order Details</h2>

        <div class="info">
            <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
            <p><strong>Order Date:</strong> <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($order['order_status']) ?></p>
            <p><strong>Total:</strong> KES <?= number_format($order['total_amount'], 2) ?></p>
        </div>

        <div class="items">
            <h3>Items in this Order:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price (KES)</th>
                        <th>Subtotal (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    while ($item = $items_result->fetch_assoc()):
                        $subtotal = $item['price'] * $item['quantity'];
                        $grand_total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['food_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                            <td><?= number_format($subtotal, 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="3" class="total">Grand Total</td>
                        <td class="total">KES <?= number_format($grand_total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a href="my_orders.php" class="btn-back">← Back to My Orders</a>
    </div>
</body>

</html>