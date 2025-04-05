<?php include("include_front/menu.php"); ?>


<!-- food search  -->
<section class="food-search">
    <div class="container">
        <form action="food_search.php" method="POST">
            <input type="search" class="form-control" name="search" placeholder="Search for Food.." required>
            <input type="submit" name="submit" value="Search" class="btn">
        </form>
    </div>
</section>


<!-- food categories -->
<section class="categories">
    <h2 class="head text-center">Explore Foods</h2>
    <div class="container">
        <?php
        $get_category_sql = "SELECT * FROM category LIMIT 3";
        $get_category_result = mysqli_query($connect, $get_category_sql);
        if (!$get_category_result) {
            die("Get categort error: " . mysqli_error($connect));
        }
        if (mysqli_num_rows($get_category_result) > 0) {
            while ($category_row = mysqli_fetch_assoc($get_category_result)) {
                $category_id = $category_row['category_id'];
                $category_name = $category_row['category_name'];
                $category_image = $category_row['category_image'];
        ?>
                <a href="category_foods.php?category_id=<?php echo $category_id; ?>">
                    <div class="box-3 float-container">
                        <?php
                        if ($category_image !== "") {
                        ?>
                            <img src="<?php echo $category_image; ?>" class="img-responsive rounded mx-auto d-block" alt="Food Category">
                        <?php
                            // echo '<img src="$category_image" alt="Category Image" class="img-responsive rounded mx-auto d-block">';
                        } else {
                            echo "Category Image Not Found";
                        }
                        ?>
                        <h3 class="float-text"><?php echo $category_name; ?></h3>
                    </div>
                </a>
        <?php
            }
        } else {
            echo "<div class='text-center text-danger'>Categories Not Available</div>";
        }
        ?>
    </div>
</section>

<div class="clear-fix"></div>

<!-- food menu -->
<section class="food-menu">
    <div class="container">
        <h2 class="head text-center">Food Menu</h2>
        <div class="food-menu-boxes">
            <?php
            $get_food_sql = "SELECT * FROM food";
            $get_food_result = mysqli_query($connect, $get_food_sql);
            if (!$get_food_result) {
                die("Get food sql error: " . mysqli_error($connect));
            }
            if (mysqli_num_rows($get_food_result) > 0) {
                while ($food_row = mysqli_fetch_assoc($get_food_result)) {
                    $food_id = $food_row['food_id'];
                    $food_name = $food_row['food_name'];
                    $food_image = $food_row['food_image'];
                    $price = $food_row['price'];
                    $description = $food_row['description'];
            ?>
                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <?php
                            if ($food_image !== "") {
                            ?>
                                <img src="<?php echo $food_image; ?>" class="rounded" alt="Food image">
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
                echo "<div class='text-center text-danger'>Foods Not Available</div>";
            }
            ?>
        </div>
        <a href="foods.php" class="see-more">See all foods</a>
    </div>

    <!-- Vertically centered modal -->
    <div class="modal" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body cart">
                    <form action="add-to-cart.php" method="post" id="cartForm" style="text-align: center;">
                        <div class="mb-2">
                            <div class="image">
                                <img src="images/burger.jpg" alt="Food Selected Image" id="modal-food-image" width="200px" height="200px">
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
</section>

<!-- shopping cart start -->
<section>
    <?php
    $cart_items_count = 0;
    $cart_items = [];

    // check if user is logged in 
    if (isset($_SESSION['customer_id'])) {
        // user is logged in
        $customer_id = $_SESSION['customer_id'];
        $cart_get_sql = "SELECT ci.cart_item_id, ci.cart_id, ci.food_id, f.food_name AS name, f.food_image AS image, ci.quantity, ci.price 
            FROM cartitems ci 
            JOIN food f ON ci.food_id = f.food_id 
            WHERE ci.cart_id = (SELECT cart_id FROM shoppingcart WHERE customer_id = '$customer_id')";
        $cart_get_result = mysqli_query($connect, $cart_get_sql);


        if (!$cart_get_result) {
            echo "Error fetching cart item: " . mysqli_error($connect);
        } else {
            while ($cart_get_row = mysqli_fetch_assoc($cart_get_result)) {
                $cart_items[] = $cart_get_row;
            }
        }
    } else if (isset($_SESSION['cart'])) {
        // no user has logged in yet store the cartitems in a session
        $cart_items = $_SESSION['cart'];
    }

    $cart_items_count = count($cart_items);

    ?>
    <div class="shopping-carts">
        <span class="number" style="background-color: #ff4757"><?php echo $cart_items_count; ?></span>
        <button type="button" class="btn-create " data-bs-toggle="modal" data-bs-target="#shoppingCartModal"
            style="padding: 15px 20px;"> <i class="fa-solid fa-cart-shopping"></i></button>
    </div>

    <!-- view cart start -->
    <div class="modal custom-modal-height" id="shoppingCartModal" tabindex="-1" aria-labelledby="cartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-width">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title " id="shoppingCartLabel">Your Cart</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                if (isset($_SESSION['success'])) {
                    echo "
                             <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                 {$_SESSION['success']}
                                  <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                 </button>
                             </div>
                         ";
                    unset($_SESSION['success']);
                }
                ?>
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
                                    foreach ($cart_items as $item) {
                                        // echo '<pre>';
                                        // print_r($item);
                                        // echo '</pre>';
                                        $cart_item_id = isset($item['cart_item_id']) ? $item['cart_item_id'] : (isset($item['id']) ? $item['id'] : '');
                                        $food_id = isset($item['food_id']) ? $item['food_id'] : (isset($item['id']) ? $item['id'] : '');
                                        $food_name = isset($item['name']) ? $item['name'] : '';
                                        $food_image = isset($item['image']) ? $item['image'] : '';
                                        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                                        $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                                        $total_price = $price * $quantity;
                                        $total_cost += $total_price;

                                ?>
                                        <tr>
                                            <td class="flex">
                                                <img src="<?php echo htmlspecialchars($food_image); ?>" alt="<?php echo htmlspecialchars($food_name); ?>">
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
</section>

<?php include("include_front/footer.php"); ?>