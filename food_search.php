<?php include("include_front/menu.php"); ?>

<!-- food section start -->
<section class="food-search text-center">
    <div class="container">
        <?php
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
            echo '<h2>Foods on Your Search <a href="#" class="text-white" style="text-decoration: none;">"' . htmlspecialchars($search) . '"</a></h2>';
        } else {
            echo '<h2>No search term provided.</h2>';
            $search = "";
        }
        ?>
        <!-- <h2>Foods on Your Search <a href="#" class="text-white" style="text-decoration: none;">" <?php echo $search; ?> "</a></h2> -->
    </div>
</section>
<!-- food section end -->

<!-- food menu section start -->
<section class="food-menu" style="height: auto; margin-top: 20px;">
    <div class="container">
        <h2 class="text-center head">Food Menu</h2>
        <?php
        if ($search != "") {

            $food_search_sql = "SELECT * FROM food WHERE food_name LIKE '%$search%' OR description LIKE '%$search%'";

            $food_search_result = mysqli_query($connect, $food_search_sql);

            $food_search_count = mysqli_num_rows($food_search_result);

            if ($food_search_count > 0) {

                while ($food_search_row = mysqli_fetch_assoc($food_search_result)) {

                    $food_id = $food_search_row['food_id'];
                    $food_name = $food_search_row['food_name'];
                    $food_image = $food_search_row['food_image'];
                    $price = $food_search_row['price'];
                    $description = $food_search_row['description'];

        ?>
                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <?php
                            if ($food_image !== "") {
                            ?>
                                <img src="images/food/<?php echo $food_image; ?>" class="rounded" alt="Food image">
                            <?php
                            } else {
                                echo "Food Image Not available";
                            }
                            ?>
                        </div>
                        <div class="food-menu-desc">
                            <h4><?php echo $food_name; ?></h4>
                            <p class="food-price">Ksh <?php echo $price; ?></p>
                            <p class="food-detail"><?php echo $description; ?></p>
                            <button type="button" class="button add"
                                data-bs-toggle="modal" data-bs-target="#cartModal"
                                data-food-id="<?php echo $food_id; ?>"
                                data-food-name="<?php echo $food_name; ?>"
                                data-food-price="<?php echo $price; ?>"
                                data-food-description="<?php echo $description; ?>"
                                data-food-image="<?php echo $food_image; ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
        <?php
                }
            } else {
                echo "<div class='alert alert-danger text-center' role='alert'>Food Not Found</div>";
            }
        }

        ?>

    </div>

    <div class="clearfix"></div>
    <!-- Vertically centered modal -->
    <div class="modal" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body cart">
                    <form action="add-to-cart.php" method="post" id="cartForm">
                        <div class="mb-2">
                            <div class="image">
                                <img src="images/burger.jpg" alt="Food Selected Image" id="modal-food-image">
                            </div>
                        </div>
                        <div class="mb-2">
                            <input type="hidden" name="food_id" id="modal-food-id">
                            <input type="hidden" name="food_name" id="modal-food-name-input">
                            <input type="hidden" name="price" id="modal-food-price-input">
                            <input type="hidden" name="food_image" id="modal-food-image-input">

                            <div class="food-name" id="modal-food-name">Burger</div>
                            <div class="food-price" id="modal-food-price">ksh 100</div>
                        </div>
                        <div class="mb-2">
                            <div class="description" id="modal-food-description">Lorem ipsum dolor sit, amet consectetur adipisicing elit.</div>
                        </div>
                        <div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn">Add to Cart</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- view food section end -->

    <?php
    $cart_items_count = 0;
    $cart_items = [];

    if (isset($_SESSION['customer_id'])) {
        // user is logged in
        $customer_id = $_SESSION['customer_id'];
        $cart_get_sql =  "SELECT ci.cart_item_id, ci.cart_id, ci.food_id, f.food_name AS name, f.food_image AS image, ci.quantity, ci.price 
                                    FROM cartitems ci 
                                    JOIN food f ON ci.food_id = f.food_id 
                                    WHERE ci.cart_id = (SELECT cart_id FROM shoppingcart WHERE customer_id = '$customer_id')";
        $cart_get_result = mysqli_query($connect, $cart_get_sql);

        if (!$cart_get_result) {
            echo "Error fetching cart items: " . mysqli_error($connect);
        } else {
            while ($cart_get_row = mysqli_fetch_assoc($cart_get_result)) {
                $cart_items[] = $cart_get_row;
            }
        }
    } else if (isset($_SESSION['cart'])) {
        // no user is logged in so store cart itms in a session 
        $cart_items = $_SESSION['cart'];
    }

    $cart_items_count = count($cart_items);
    ?>
    <!-- shopping cart start -->
    <div class="shopping-carts">
        <span class="number" style="background-color: #ff4757"><?php echo $cart_items_count; ?></span>
        <button type="button" class="btn-create " data-bs-toggle="modal" data-bs-target="#shoppingCartModal"
            style="padding: 15px 20px;"> <i class="fa-solid fa-cart-shopping"></i></button>
    </div>
    <!-- shopping cart end -->

    <!-- view_cart start  -->
    <div class="modal custom-modal-height" id="shoppingCartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-width">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title " id="shoppingCartLabel">Your Cart</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body shop">
                    <table>
                        <thead>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <form action="update_cart.php" method="post">
                                <?php
                                $total_cost = 0;

                                if (!empty($cart_items)) {
                                    // cart items exist
                                    foreach ($cart_items as $item) {
                                        $cart_item_id = isset($item['cart_item_id']) ? $item['cart_item_id'] : (isset($item['id']) ? $item['id'] : '');
                                        $food_id = isset($item['food_id']) ? $item['food_id'] : (isset($item['id']) ? $item['id'] : '');
                                        $food_name = isset($item['name']) ? $item['name'] : '';
                                        $food_image = isset($item['image']) ? $item['image'] : '';
                                        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                                        $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                                        $total_price = $price * $quantity;
                                        $total_cost += $total_price;

                                        if (strpos($food_image, 'images/food/') === false) {
                                            $food_image = 'images/food/' . $food_image;
                                        }
                                ?>
                                        <tr>
                                            <td class="flex">
                                                <img src="<?php echo htmlspecialchars($food_image); ?>"" alt=" <?php echo htmlspecialchars($food_name); ?>">
                                                <div class="name"><?php echo $food_name; ?></div>
                                            </td>
                                            <td>Ksh<?php echo $price; ?></td>
                                            <input type="hidden" id="food-price-<?php echo $food_id; ?>" value="<?php echo $price; ?>">
                                            <td class="quantity">
                                                <button type="button" onclick="decrementQuantity(<?php echo $food_id; ?>)">&minus;</button>
                                                <input type="text" name="quantity[<?php echo $cart_item_id; ?>]" id="quantity-display-<?php echo $food_id; ?>" min="1" value="<?php echo $quantity; ?>" onchange="updateTotalPrice(<?php echo $food_id; ?>)">
                                                <button type="button" onclick="incrementQuantity(<?php echo $food_id; ?>)">&plus;</button>
                                            </td>

                                            <td id="total-price-<?php echo $food_id; ?>">Ksh<?php echo $total_price ?></td>
                                            <input type="hidden" name="total_price[<?php echo $cart_item_id; ?>]" id="total-price-input-<?php echo $food_id; ?>" value="<?php echo $total_price; ?>">
                                            <!-- <input type="hidden" name="cart_id[<?php echo $cart_id; ?>]" id="cart-id-<?php echo $cart_id; ?>" value="<?php echo $cart_id; ?>"> -->
                                            <td>
                                                <a href="delete_food_item.php?food_id=<?php echo $food_id; ?>" id="delete"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>You Don't have items in Your Cart</td></tr>";
                                }
                                ?>

                        </tbody>
                    </table>
                    <input type="hidden" name="cart_id" value="<?php echo isset($cart_id) ? htmlspecialchars($cart_id) : ''; ?>">
                    <div class="total">
                        <div class="Subtotal">Subtotal</div>
                        <div class="subtotal-number" id="subtotal">Ksh <?php echo $total_cost; ?></div>
                        <!-- <input type="hidden" name="subtotal" value="<?php echo $total_cost; ?>"> -->
                    </div>
                </div>
                <div>
                    <div class="modal-footer">

                        <button type="submit" name="update" class="btn" data-bs-dismiss="modal">Buy More</button>
                        <button type="submit" name="submit" class="btn" onclick="handleCheckout()">Checkout</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- view_cart end -->
    </div>
</section>
<!-- food menu section end -->

<?php include("include_front/footer.php"); ?>