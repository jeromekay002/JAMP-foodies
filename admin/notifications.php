<?php
include("include_front/navbar.php");
include 'include_front/connect.php';

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}


$query = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .bg-section {
            background-image: url('../images/bg.jpg');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .table-container {
            max-width: 1100px;
            background-color: #ffffff;
            /* slightly transparent white */
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 90%;
        }

        .table thead th {
            background-color: #0d6efd;
            color: white;
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            text-align: center;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .no-notification {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #6c757d;
        }

        h4 {
            font-weight: 600;
        }
    </style>
</head>

<body>


    <div class="bg-section">
        <div class="table-container">
            <!-- <a href="admin_dashboard.php" class="btn btn-secondary back-btn">← Back to Dashboard</a> -->

            <h4 class="mb-4 text-center">🔔 Admin Notifications</h4>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Message</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>{$count}</td>";
                                echo "<td>{$row['message']}</td>";
                                echo "<td><span class='badge bg-info text-dark'>" . ucfirst($row['type']) . "</span></td>";
                                echo "<td><span class='badge " . ($row['status'] === 'unread' ? 'bg-warning' : 'bg-success') . "'>" . ucfirst($row['status']) . "</span></td>";
                                echo "<td>" . date("M d, Y h:i A", strtotime($row['created_at'])) . "</td>";
                                echo "</tr>";
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-notification">No notifications found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>