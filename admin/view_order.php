<?php
session_start();
include("include_front/connect.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header("Location: orders.php");
    exit();
}

// Fetch order and items
$query = "
    SELECT 
        orders.*, 
        orderitems.*, 
        customers.full_name AS customer_name,
        customers.email AS customer_email,
        customers.phone_number AS customer_phone
    FROM orders
    JOIN orderitems ON orders.order_id = orderitems.order_id
    JOIN customers ON orders.customer_id = customers.customer_id
    WHERE orders.order_id = ?
";

$stmt = $connect->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_details = [];
$order_info = null;

while ($row = $result->fetch_assoc()) {
    if (!$order_info) {
        $order_info = $row; // first row holds main order info
    }
    $order_details[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../images/bg.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            height: 100vh;
        }

        :root {
            --theme-color: #ff4757;
        }

        .theme-header {
            background-color: var(--theme-color);
            color: white;
        }

        .badge-status {
            font-size: 0.9rem;
        }

        .update-section {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: #f9f9f9;
            max-width: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .update-section h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }

        .update-section form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .update-section label {
            font-weight: bold;
            color: #555;
        }

        .update-section select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .update-section .update-btn {
            background-color: #ff4757;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .update-section .update-btn:hover {
            color: #000;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container m-5">
        <?php if ($order_info): ?>
            <div class="card mb-4">
                <div class="card-header theme-header">
                    <h4 class="mb-0">📄 Order #<?= $order_info['order_id'] ?> Details</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">Customer Information</h5>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order_info['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order_info['customer_email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order_info['customer_phone']) ?></p>

                    <hr>

                    <h5 class="mb-3">Order Summary</h5>
                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order_info['delivery_address']) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order_info['payment_method']) ?></p>
                    <p><strong>Payment Status:</strong>
                        <span class="badge bg-<?= $order_info['payment_status'] === 'paid' ? 'success' : 'warning' ?> badge-status">
                            <?= ucfirst($order_info['payment_status']) ?>
                        </span>
                    </p>
                    <p><strong>Order Status:</strong>
                        <span class="badge bg-<?= $order_info['order_status'] === 'pending' ? 'warning' : 'success' ?> badge-status">
                            <?= ucfirst($order_info['order_status']) ?>
                        </span>
                    </p>
                    <p><strong>Total Amount:</strong>Ksh <?= number_format($order_info['total_amount'], 2) ?></p>
                    <p><strong>Ordered On:</strong> <?= date('d M Y, h:i A', strtotime($order_info['created_at'])) ?></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">🧾 Ordered Items</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Food Image</th>
                                <th>Food Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_details as $item): ?>
                                <tr>
                                    <td>
                                        <img src="../images/food/<?= htmlspecialchars($item['food_image']) ?>" width="60" height="60" alt="">
                                    </td>
                                    <td><?= htmlspecialchars($item['food_name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>Ksh<?= number_format($item['price'], 2) ?></td>
                                    <td>Ksh<?= number_format($item['total_price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">Order not found.</div>
        <?php endif; ?>


        <hr>
        <div class="update-section">
            <h2>Update Order Status</h2>
            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $order_info['order_id'] ?>">
                <label for="new_status">New Status:</label>
                <select name="new_status" id="new_status" required>
                    <?php
                    $statuses = [
                        'Order Placed',
                        'Preparing',
                        'Ready for Pickup',
                        'Out for Delivery',
                        'Delivered'
                    ];
                    // Put current status first
                    echo "<option value=\"{$order_info['order_status']}\" selected>{$order_info['order_status']}</option>";
                    foreach ($statuses as $status) {
                        if ($status !== $order_info['order_status']) {
                            echo "<option value=\"$status\">$status</option>";
                        }
                    }
                    ?>
                </select>

                <button type="submit" name="update_status" class="update-btn">Update</button>
            </form>

        </div>

        <?php
    
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
            $new_status = mysqli_real_escape_string($connect, $_POST['new_status']);
            $order_id = intval($_POST['order_id']);

            $update_query = "UPDATE orders SET order_status = '$new_status' WHERE order_id = $order_id";

            if (mysqli_query($connect, $update_query)) {
                echo "<script>alert('Order status updated successfully!'); window.location.href = 'view_order.php?order_id=$order_id';</script>";
                exit;
            } else {
                echo "<script>alert('Failed to update order status.');</script>";
            }
        }
        ?>

        <hr>
        <a href="orders.php" class="btn mt-4" style="background-color: #ff4757; color: #fff; display: flex; justify-content: center;">← Back to Orders</a>

    </div>
</body>

</html>