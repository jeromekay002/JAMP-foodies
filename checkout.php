<?php
// session_start();
include("include_front/menu.php");

if (!$connect) {
  die("Connection failed: " . mysqli_connect_error());
}

$current_page = 'checkout.php';

if (isset($_SESSION['redirect_from_checkout']) && $_SESSION['redirect_from_checkout']) {
  unset($_SESSION['redirect_from_checkout']);
  $_SESSION['success'] = "<p class='alert alert-success text-center'>You have successfully logged in</p>";
}
?>

<div class="container">
  <section class="check-out food-search">
    <div class="containers">
      <header class="checkout-header">Checkout</header>
      <?php
      if (isset($_SESSION['payment'])) {
        echo $_SESSION['payment'];
        unset($_SESSION['payment']);
      }
    

      if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];

        // Fetch customer details
        $customer_details_sql = "SELECT * FROM customers WHERE customer_id='$customer_id'";
        $customer_details_result = mysqli_query($connect, $customer_details_sql);

        if ($customer_details_result && mysqli_num_rows($customer_details_result) == 1) {
          $customer_details_row = mysqli_fetch_assoc($customer_details_result);

          $full_name = $customer_details_row['full_name'];
          $email = $customer_details_row['email'];
          $address = $customer_details_row['delivery_address'];
          $phone_number = $customer_details_row['phone_number'];
        } else {
          echo "<p>Customer details not found</p>";
          $full_name = $email = $city = $address = $phone_number = '';
        }

        // Fetch shopping cart details
        $shopping_cart_sql = "SELECT cart_id FROM shoppingcart WHERE customer_id='$customer_id'";
        $shopping_cart_result = mysqli_query($connect, $shopping_cart_sql);

        if ($shopping_cart_result && mysqli_num_rows($shopping_cart_result) == 1) {
          $shopping_cart_row = mysqli_fetch_assoc($shopping_cart_result);
          $cart_id = $shopping_cart_row['cart_id'];

          // Fetch cart items
          $cart_items_sql = "SELECT * FROM cartitems WHERE cart_id='$cart_id'";
          $cart_items_result = mysqli_query($connect, $cart_items_sql);

          if ($cart_items_result) {
            $cart_items_count = mysqli_num_rows($cart_items_result);

            $total_cost = 0;

            // Calculate total cost and vat amount
            while ($cart_items_row = mysqli_fetch_assoc($cart_items_result)) {
              $total_price = $cart_items_row['total_price'];
              $total_cost += $total_price; // Updated to accumulate total cost
            }

            $vat_percentage = 0.16; // 16%
            $vat_amount = $total_cost * $vat_percentage;
            $subtotal = $total_cost - $vat_amount;
            $total_amount = $subtotal + $vat_amount;
          } else {
            $cart_items_count = 0;
            $total_cost = $vat_amount = $subtotal = $total_amount = 0;
          }
        } else {
          $cart_items_count = 0;
          $total_cost = $vat_amount = $subtotal = $total_amount = 0;
        }
      } else {
        $current_page = 'checkout.php';
        echo "<p class='alert alert-danger text-center'>Please login to continue</p>";
        $cart_items_count = $total_cost = $vat_amount = $subtotal = $total_amount = 0;
      }
      ?>
      <form id="checkout-form" action="process_checkout.php" method="post">
        <div class="main-checkout">
          <div class="customer-details">
            <div class="mb-2">
              <div>
                <?php
                if (isset($_SESSION['fullName'])) {
                  echo $_SESSION['fullName'];
                  unset($_SESSION['fullName']);
                }
                ?>
              </div>
              <input type="hidden" name="customer-id" value="<?php echo $customer_id; ?>">
              <label for="" class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="<?php echo isset($full_name) ? $full_name : ''; ?>" autocomplete="off" required>
            </div>

            <div class="mb-2">
              <div>
                <?php
                if (isset($_SESSION['email'])) {
                  echo $_SESSION['email'];
                  unset($_SESSION['email']);
                }
                ?>
              </div>
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>" autocomplete="off" required>
              <div id="email_error" class="invalid-feedback" style="display: none;">Please enter a valid email address.</div>
            </div>
  
            <div class="mb-2">
              <label for="" class="form-label">Delivery Address</label>
              <input type="text" name="delivery_address" class="form-control" value="<?php echo isset($address) ? $address : ''; ?>" placeholder="Machakos Next to Naivas" autocomplete="off" required>
            </div>

            <div class="mb-2">
              <label for="phone_number" class="form-label">Phone Number</label>
              <input type="text" name="phone_number" id="phone_number_checkout" class="form-control"  value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>" autocomplete="off" required>
              <div id="phone_error_checkout" class="invalid-feedback" style="display: none;">Please enter a valid phone number.</div>
            </div>

            <div class="mb-2">
              <label for="" class="form-label">Select Your Payment Method</label>
              <select name="payment_method" id="" class="form-control">
                <option value="Mpesa">Mpesa</option>
                <option value="Cash">Cash on Delivery</option>
              </select>
            </div>
          </div>

          <div class="food-details">
            <header>Your Cart <span>(<?php echo $cart_items_count; ?> Items)</span></header>
            <table>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($cart_items_count > 0) {
                  mysqli_data_seek($cart_items_result, 0);
                  while ($cart_items_row = mysqli_fetch_assoc($cart_items_result)) {
                    $food_id = $cart_items_row['food_id'];
                    $food_name = $cart_items_row['food_name'];
                    $quantity = $cart_items_row['quantity'];
                    $price = $cart_items_row['price'];
                    $total_price = $cart_items_row['total_price'];
                ?>
                    <tr>
                      <td class="flex">
                        <input type="hidden" name="food_id[]" value="<?php echo $food_id; ?>">
                        <div class="food-name" style="font-size: 18px;"><?php echo $food_name; ?> * <?php echo $quantity; ?></div>
                        <input type="hidden" name="food_name[]" value="<?php echo $food_name; ?>">
                        <input type="hidden" name="quantity[]" value="<?php echo $quantity; ?>">
                        <input type="hidden" name="food_price[]" value="<?php echo $price; ?>">
                      </td>
                      <td>
                        Ksh <?php echo $total_price; ?>
                        <input type="hidden" name="total_price[]" value="<?php echo $total_price; ?>">
                      </td>
                    </tr>
                <?php
                  }
                } else {
                  echo "<tr><td colspan='2' class='text-center text-danger'>No items in cart</td></tr>";
                }
                ?>
              </tbody>
            </table>

            <div class="other-pay">
              <div class="flex">
                <div class="total">Subtotal</div>
                <div class="number">Ksh <?php echo $subtotal; ?></div>
                <input type="hidden" name="total_cost" value="<?php echo $subtotal; ?>">
              </div>
              <div class="flex">
                <div class="total">VAT 16%</div>
                <div class="number">Ksh <?php echo $vat_amount; ?></div>
                <input type="hidden" name="vat_amount" value="<?php echo $vat_amount; ?>">
              </div>
              <div class="flex">
                <div class="total">Total</div>
                <div class="number">Ksh <?php echo $total_amount; ?></div>
                <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
              </div>
            </div>
          </div>
          <button type="submit" name="submit" class="checkout-btn">Place Order</button>
      </form>
    </div>
  </section>

  <?php include("include_front/footer.php"); ?>