<?php
include("include_front/navbar.php");
?>

<!-- food search  -->
<section class="orders-management">
    <div class="orders-container">

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="card-box">
                <i class="fas fa-clock"></i>
                <div>
                    <h3>Received Orders</h3>
                    <p>
                        <?php
                        $pending_orders_sql = "SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Received' OR order_status = 'Order Placed'";
                        $pending_orders_res = mysqli_query($connect, $pending_orders_sql);
                        if ($pending_orders_res) {
                            $pending_orders_row = mysqli_fetch_assoc($pending_orders_res);
                            echo $pending_orders_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="card-box">
                <i class="fas fa-utensils"></i>
                <div>
                    <h3>Preparing Orders</h3>
                    <p>
                        <?php
                        $preparing_orders_sql = "SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Preparing'";
                        $preparing_orders_res = mysqli_query($connect, $preparing_orders_sql);
                        if ($preparing_orders_res) {
                            $preparing_orders_row = mysqli_fetch_assoc($preparing_orders_res);
                            echo $preparing_orders_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="card-box">
                <i class="fas fa-check-circle"></i>
                <div>
                    <h3>Delivered Orders</h3>
                    <p>
                        <?php
                        $delivered_orders_sql = "SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Delivered'";
                        $delivered_orders_res = mysqli_query($connect, $delivered_orders_sql);
                        if ($delivered_orders_res) {
                            $delivered_orders_row = mysqli_fetch_assoc($delivered_orders_res);
                            echo $delivered_orders_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="card-box">
                <i class="fas fa-times-circle"></i>
                <div>
                    <h3>Cancelled Orders</h3>
                    <p>
                        <?php
                        $cancelled_orders_sql = "SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Cancelled'";
                        $cancelled_orders_res = mysqli_query($connect, $cancelled_orders_sql);
                        if ($cancelled_orders_res) {
                            $cancelled_orders_row = mysqli_fetch_assoc($cancelled_orders_res);
                            echo $cancelled_orders_row['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-bar" style="margin-top: 30px;">
            <input type="text" placeholder="Search orders...">
            <button><i class="fas fa-search"></i></button>
        </div>

        <!-- Table Header -->
        <div class="table-header">
            <h3><i class="fas fa-list"></i> Orders List</h3>
            <button><i class="fas fa-download"></i> Export</button>
        </div>

        <!-- Orders Table -->
        <div class="table-wrapper">
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
        </div>

    </div>
</section>

<!-- Add Food Modal -->
<div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" style="border-bottom: none; position: relative;">
                <h1 id="addFoodModalLabel">Add New Food</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="addFoodForm" enctype="multipart/form-data">

                    <div class="mb-3">
                        <i class="fas fa-hamburger"></i>
                        <input type="text" name="food_name" placeholder="Food Name" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-th-large"></i>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <option value="Burgers">Burgers</option>
                            <option value="Fries">Fries</option>
                            <option value="Drinks">Drinks</option>
                            <option value="Pizza">Pizza</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-dollar-sign"></i>
                        <input type="number" name="price" placeholder="Price" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-box"></i>
                        <input type="number" name="quantity" placeholder="Stock Quantity" required>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-align-left"></i>
                        <input type="text" name="description" placeholder="Description">
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-check-circle"></i>
                        <select name="status" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-image"></i>
                        <input type="file" name="food_image" accept="image/*" required>
                    </div>

                    <div class="button-field">
                        <button type="submit" class="button">
                            <i class="fas fa-save"></i> Save Food
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<?php
include("include_front/footer.php");
?>