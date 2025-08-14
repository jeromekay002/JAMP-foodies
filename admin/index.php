<?php
include("include_front/navbar.php");
?>

<!-- Dashboard Section -->
<section class="orders-management">
    <div class="orders-container">
        <!-- Dashboard Header -->
        <div class="header">
            <h2>Dashboard</h2>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="card-box">
                <i class="fas fa-hamburger"></i>
                <div>
                    <h3>Food</h3>
                    <p>
                        <?php
                        $food_count_sql = "SELECT COUNT(*) AS total FROM food";
                        $food_count_res = mysqli_query($connect, $food_count_sql);
                        if ($food_count_res) {
                            $food_count_row = mysqli_fetch_assoc($food_count_res);
                            echo $food_count_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="card-box">
                <i class="fas fa-th-large"></i>
                <div>
                    <h3>Category</h3>
                    <p>
                        <?php
                        $category_count_sql = "SELECT COUNT(*) AS total FROM category";
                        $category_count_res = mysqli_query($connect, $category_count_sql);
                        if ($category_count_res) {
                            $category_count_row = mysqli_fetch_assoc($category_count_res);
                            echo $category_count_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="card-box">
                <i class="fas fa-receipt"></i>
                <div>
                    <h3>Orders</h3>
                    <p>
                        <?php
                        $orders_count_sql = "SELECT COUNT(*) AS total FROM orders";
                        $orders_count_res = mysqli_query($connect, $orders_count_sql);
                        if ($orders_count_res) {
                            $orders_count_row = mysqli_fetch_assoc($orders_count_res);
                            echo $orders_count_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="card-box">
                <i class="fas fa-users"></i>
                <div>
                    <h3>Customers</h3>
                    <p>
                        <?php
                        $customers_count_sql = "SELECT COUNT(*) AS total FROM customers";
                        $customers_count_res = mysqli_query($connect, $customers_count_sql);
                        if ($customers_count_res) {
                            $customers_count_row = mysqli_fetch_assoc($customers_count_res);
                            echo $customers_count_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <section class="recent-orders">
            <h2>Recent Orders</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $get_orders_sql = "
                            SELECT 
                                o.order_id,
                                c.full_name AS customers,
                                COUNT(oi.order_item_id) AS items,
                                o.order_status AS status,
                                o.total_amount AS total,
                                o.created_at AS date
                            FROM orders o
                            JOIN customers c 
                                ON o.customer_id = c.customer_id
                            LEFT JOIN orderitems oi 
                                ON o.order_id = oi.order_id
                            GROUP BY 
                                o.order_id, c.full_name, o.order_status, o.total_amount, o.created_at
                            ORDER BY 
                                o.created_at DESC
                        ";
                    $get_orders_res = mysqli_query($connect, $get_orders_sql);
                    if (!$get_orders_res) {
                        die("Error fetching orders: " . mysqli_error($connect));
                    }
                    if (mysqli_num_rows($get_orders_res) > 0) {
                        while ($row = mysqli_fetch_assoc($get_orders_res)) {
                    ?>
                            <tr>
                                <td data-label="Order ID"><?php echo $row['order_id']; ?></td>
                                <td data-label="Customer"><?php echo $row['customers']; ?></td>
                                <td data-label="Items"><?php echo $row['items']; ?></td>
                                <td data-label="Status">
                                    <?php
                                    $status = $row['status'];

                                    switch (strtolower($status)) {
                                        case "received":
                                            $class = "pending";
                                            break;
                                        case "preparing":
                                            $class = "preparing";
                                            break;
                                        case "ready for pickup":
                                            $class = "preparing"; // same style as preparing
                                            break;
                                        case "delivered":
                                            $class = "delivered";
                                            break;
                                        case "cancelled":
                                            $class = "cancelled";
                                            break;
                                        default:
                                            $class = "unknown"; // for any unexpected status
                                            break;
                                    }

                                    echo "<span class='status $class'>" . htmlspecialchars($status) . "</span>";
                                    ?>
                                </td>
                                <td data-label="Total">Ksh<?php echo number_format($row['total'], 2); ?></td>
                                <td data-label="Date"><?php echo date("Y-m-d", strtotime($row['date'])); ?></td>
                                <td data-label="Action">
                                    <a href="view_order.php?order_id=<?= $row['order_id'] ?>" class="view-btn">View</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr>
                                <td colspan='7' class='text-center text-danger'>Orders Not Available</td>
                            </tr>";
                    }

                    ?>

                </tbody>
            </table>
        </section>
    </div>
</section>

<?php
include("include_front/footer.php");
?>