<?php
session_start();
include("include_front/connect.php");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php#loginSection");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch orders for this customer
$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .orders-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        th,
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .status-badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
            text-transform: capitalize;
        }

        .Order\ Placed {
            background-color: #ffc107;
            color: #fff;
        }

        .Preparing {
            background-color: #17a2b8;
            color: #fff;
        }

        .Ready\ for\ Pickup {
            background-color: #20c997;
            color: #fff;
        }

        .Out\ for\ Delivery {
            background-color: #fd7e14;
            color: #fff;
        }

        .Delivered {
            background-color: #28a745;
            color: #fff;
        }

        .view-btn {
            background-color: #ff4757;
            ;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .view-btn:hover {
            background-color: #0056b3;
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

        @media screen and (max-width: 768px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
            }

            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>

<body>

    <div class="orders-container">
        <h2>My Orders</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 0; ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Order ID"><?= htmlspecialchars($count + 1 ) ?></td>
                            <td data-label="Date"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                            <td data-label="Total Amount">KSh <?= number_format($order['total_amount'], 2) ?></td>
                            <td data-label="Status">
                                <span class="status-badge <?= $order['order_status'] ?>">
                                    <?= htmlspecialchars($order['order_status']) ?>
                                </span>
                            </td>
                            <td data-label="Action">
                                <a href="order_details.php?order_id=<?= $order['order_id'] ?>">
                                    <button class="view-btn">View</button>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">You have no orders yet.</p>
        <?php endif; ?>

        <a href="my_account.php" class="btn-back">← Go Back</a>
    </div>

</body>

</html>