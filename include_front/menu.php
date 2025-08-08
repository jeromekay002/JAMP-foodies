<?php
session_start();

include_once('include_front/connect.php');
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
// check if the user in the checkout page 
$current_page = basename($_SERVER['PHP_SELF']);
$show_login_link = $current_page === 'checkout.php' && !isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JAMP Foodies</title>
    <link rel="stylesheet" href="styles/styles.css">
    <!-- bootstrap 5 link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous" />

    <!-- link to font awesome for icons -->
    <script src="https://kit.fontawesome.com/caac21bd1e.js" crossorigin="anonymous"></script>

    <!-- link to js functions -->
    <script src="functions.js"></script>
    <script>
        document.getElementById('phone_number').addEventListener('input', function(e) {
            let input = e.target.value;
            let phoneError = document.getElementById('phone_error');

            phoneError.style.display = 'none';

            if (input.length === 10 && input.startsWith('07')) {
                e.target.value = '254' + input.substring(1);
            } else if (input.length === 10 && !input.startsWith('07')) {
                phoneError.textContent = 'Invalid Phone Number Format';
                phoneError.style.display = 'block';
            } else if (input.length !== 10 && !input.startsWith('254')) {
                phoneError.textContent = 'Phone Number must be 10 digits long and start with "07".';
                phoneError.style.display = 'block';
            }
        });
    </script>
</head>

<body>
    <!-- menu start -->
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <h3>Jamp F<span class="logo-letters">oo</span>dies</h3>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="category.php">Category</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="foods.php">Foods</a>
                        </li>
                        <li class="nav-item">
                            <div class="shopping-cart">
                                <a class="nav-link btn create" href="view_cart.php" data-bs-toggle="modal" data-bs-target="#shoppingCartModal">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                            </div>
                        </li>

                        <!-- login check -->
                        <?php if (!isset($_SESSION['customer_id'])) : ?>
                            <?php if ($current_page === 'checkout.php') : ?>
                                <!-- Show checkout login only on checkout page -->
                                <li class="nav-item">
                                    <button type="button" class="btn create" data-bs-toggle="modal" data-bs-target="#checkoutloginSection">Login</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="btn create" data-bs-toggle="modal" data-bs-target="#checkoutCreateAccountSection">Create Account</button>
                                </li>
                            <?php else : ?>
                                <!-- Show general login on other pages -->
                                <li class="nav-item">
                                    <button type="button" class="btn create" data-bs-toggle="modal" data-bs-target="#loginSection">Login</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="btn create" data-bs-toggle="modal" data-bs-target="#createAccountSection">Create Account</button>
                                </li>
                            <?php endif; ?>

                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link btn create" href="my_account.php" style="padding: 10px 20px;">My Account</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link create" href="include_front/logout.php" style="padding: 10px 20px;">Log Out</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- login section start -->
        <div class="modal fade" id="loginSection" tabindex="-1" aria-labelledby="loginSectionLabel" aria-hidden="true" <?php if (isset($_SESSION['login_errors'])) echo 'data-bs-show="true"'; ?>>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title " id="loginSectionLabel">Login</h1>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body">
                        <form action="login.php" method="post" autocomplete="off">
                            <?php if (isset($_SESSION['login_errors']['email'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['login_errors']['email']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="text" name="email" class="form-control" placeholder="Enter Email Address" autocomplete="off" required>
                            </div>

                            <?php if (isset($_SESSION['login_errors']['password'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['login_errors']['password']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password" id="loginPasswordInput" autocomplete="off" required>
                                <i class="fa fa-eye password-toggle" style="margin-right: 10px;"></i>
                            </div>

                            <p class="already">Don't Have an account<button type="button" class="btn create" data-bs-toggle="modal" data-bs-target="#createAccountSection">Click Here</button></p>
                            <!-- <p class="already">Don't Have an account<button>Click Here</button></p> -->

                            <div class="button-field">
                                <button type="submit" name="submit" class="button">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- login section end -->

        <!-- Create Account Section Start -->
        <div class="modal fade" id="createAccountSection" tabindex="-1" aria-labelledby="loginSectionLabel" aria-hidden="true" <?php if (isset($_SESSION['register_errors'])) echo 'data-bs-show="true"'; ?>>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="loginSectionLabel">Create Account</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="register.php" method="post" autocomplete="off">
                            <?php if (isset($_SESSION['register_errors']['full_name'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['full_name']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" name="full_name" class="form-control" placeholder="Enter Full Name" autocomplete="off" value="<?php echo isset($_SESSION['old_input']['full_name']) ? $_SESSION['old_input']['full_name'] : ''; ?>" required>
                            </div>
                            <?php if (isset($_SESSION['register_errors']['email'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['email']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="text" name="email" class="form-control" placeholder="Enter Email Address" autocomplete="off" value="<?php echo isset($_SESSION['old_input']['email']) ? $_SESSION['old_input']['email'] : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <i class="fa-solid fa-flag"></i>
                                <input type="text" name="delivery_address" id="create-account-current_location" class="address form-control" placeholder="Delivery Address" value="<?php echo isset($_SESSION['old_input']['delivery_address']) ? $_SESSION['old_input']['delivery_address'] : ''; ?>" required>
                            </div>
                            <div class="invalid-feedback d-block" id="phone_error"></div>
                            <?php if (isset($_SESSION['register_errors']['phone_number'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['phone_number']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Enter Phone Number: 07****" autocomplete="off" value="<?php echo isset($_SESSION['old_input']['phone_number']) ? $_SESSION['old_input']['phone_number'] : ''; ?>" required>
                            </div>
                            <?php if (isset($_SESSION['register_errors']['password'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['password']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" id="passwordInput" autocomplete="off" required>
                                <i class="fa fa-eye password-toggle" style="margin-right: 10px;"></i>
                            </div>
                            <?php if (isset($_SESSION['register_errors']['confirm_password'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['confirm_password']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" id="confirmPasswordInput" autocomplete="off" required>
                                <i class="fa fa-eye password-toggle" style="margin-right: 10px;"></i>
                            </div>
                            <p class="already">Already Have an account? <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#loginSection">Click here</button></p>
                            <div class="button-field">
                                <button type="submit" name="submit" class="button" onclick="return validatePhoneNumber()">Sign Up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Create Account Section End -->


        <!-- checkout login start -->
        <div class="modal fade" id="checkoutloginSection" tabindex="-1" aria-labelledby="checkoutloginSectionLabel" aria-hidden="true" <?php if (isset($_SESSION['login_errors'])) echo 'data-bs-show="true"'; ?>>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title " id="checkoutloginSectionLabel">Login</h1>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body">
                        <form action="login.php" method="post" autocomplete="off">
                            <?php if (isset($_SESSION['login_errors']['email'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['login_errors']['email']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="text" name="email" class="form-control" placeholder="Enter Email Address" autocomplete="off" required>
                            </div>

                            <?php if (isset($_SESSION['login_errors']['password'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['login_errors']['password']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password" id="checkoutPasswordInput" autocomplete="off" required>
                                <i class="fa fa-eye password-toggle" style="margin-right: 10px;"></i>
                            </div>

                            <p class="already">Don't Have an account<button type="button" class="btn create" data-bs-toggle="modal" data-bs-target="#checkoutCreateAccountSection"">Click Here</button></p>
                        <!-- <p class=" already">Don't Have an account<button>Click Here</button></p> -->

                            <div class="button-field">
                                <button type="submit" name="checkoutLogin" class="button">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- checkout login end -->
        <!-- Checkout Create Account Section Start -->
        <div class="modal fade" id="checkoutCreateAccountSection" tabindex="-1" aria-labelledby="checkoutCreateAccountSectionLabel" aria-hidden="true" <?php if (isset($_SESSION['register_errors'])) echo 'data-bs-show="true"'; ?>>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="checkoutCreateAccountSectionLabel">Create Account</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="register.php" method="post" autocomplete="off">
                            <?php if (isset($_SESSION['register_errors']['full_name'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['full_name']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" name="full_name" class="form-control" placeholder="Enter Full Name" autocomplete="off" value="<?php echo isset($_SESSION['old_input']['full_name']) ? $_SESSION['old_input']['full_name'] : ''; ?>" required>
                            </div>
                            <?php if (isset($_SESSION['register_errors']['email'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['email']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="text" name="email" class="form-control" placeholder="Enter Email Address" autocomplete="off" value="<?php echo isset($_SESSION['old_input']['email']) ? $_SESSION['old_input']['email'] : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <i class="fa-solid fa-flag"></i>
                                <input type="text" name="delivery_address" id="checkout-county-select" class="address form-control" placeholder="Delivery Address" value="<?php echo isset($_SESSION['old_input']['delivery_address']) ? $_SESSION['old_input']['delivery_address'] : ''; ?>" required>
                            </div>

                            <div class="invalid-feedback d-block" id="checkout_phone_error"></div>
                            <?php if (isset($_SESSION['register_errors']['phone_number'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['phone_number']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" name="phone_number" id="checkout_phone_number" class="form-control" placeholder="Enter Phone Number: 07****" autocomplete="off" value="<?php echo isset($_SESSION['old_input']['phone_number']) ? $_SESSION['old_input']['phone_number'] : ''; ?>" required>
                            </div>
                            <?php if (isset($_SESSION['register_errors']['password'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['password']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" id="checkoutpasswordInput" autocomplete="off" required>
                                <i class="fa fa-eye password-toggle" style="margin-right: 10px;"></i>
                            </div>
                            <?php if (isset($_SESSION['register_errors']['confirm_password'])) : ?>
                                <div class="invalid-feedback d-block"><?php echo $_SESSION['register_errors']['confirm_password']; ?></div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" id="checkoutconfirmPasswordInput" autocomplete="off" required>
                                <i class="fa fa-eye password-toggle" style="margin-right: 10px;"></i>
                            </div>
                            <p class="already">Already Have an account? <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#checkoutLoginSection">Click here</button></p>
                            <div class="button-field">
                                <button type="submit" class="button" name="checkoutSubmit" onclick="return validateCheckoutPhoneNumber()">Sign Up</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Checkout Create Account Section End -->
        <div id="alertMessage"></div>

        <?php
        $alerts = [
            'success' => ['type' => 'success', 'message' => $_SESSION['success'] ?? null],
            'order_success' => ['type' => 'success', 'message' => $_SESSION['order_success'] ?? null],
            'success_message' => ['type' => 'success', 'message' => $_SESSION['success_message'] ?? null],
            'error_message' => ['type' => 'danger', 'message' => $_SESSION['error_message'] ?? null],
        ];

        foreach ($alerts as $key => $alert) {
            if ($alert['message']) {
                echo "
            <div class='alert alert-{$alert['type']} alert-dismissible fade show session-alert' role='alert' style='display: flex; justify-content: space-between; transition: opacity 0.5s ease;'>
                {$alert['message']}
                <button type='button' class='close' data-dismiss='alert' aria-label='Close' style='background: transparent; border: none; font-size: 1.5rem;'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
        ";
                unset($_SESSION[$key]);
            }
        }
        ?>

        <script>
            // Auto-dismiss alert after 5 seconds
            setTimeout(function() {
                const alert = document.querySelector('.session-alert');
                if (alert) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500); // remove from DOM
                }
            }, 3000);
        </script>