<?php
session_start();
include("include_front/connect.php");

if (!$connect) {
    die("Connection Failed: " . mysqli_connect_error());
}

if (isset($_POST['submit']) || isset($_POST['checkoutSubmit'])) {
    $error = [];

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $delivery_address = $_POST['delivery_address'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. validate the full name 
    if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $errors['full_name'] = "<div class='alert alert-danger' role='alert'>Only letters are allowed.</div>";
    } else if (strlen($full_name) < 2) {
        $errors['full_name'] = "<div class='alert alert-danger' role='alert'>Full Name must be at least 2 characters.</div>";
    } else {
        $name_parts = explode(' ', $full_name);
        if (count($name_parts) < 2) {
            $errors['full_name'] = "<div class='alert alert-danger' role='alert'>Full Name should include both Your First name and Last name.</div>";
        }
    }

    // 2. validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "<div class='alert alert-danger' role='alert'>Email is invalid.</div>";
    } else {
        // Additional custom check for domain presence
        $parts = explode('@', $email);
        $domain = end($parts);
        if (!checkdnsrr($domain, 'MX')) {
            $errors['email'] = "<div class='alert alert-danger' role='alert'>Email domain is not valid</div>";
        }
    }

    // 3 check if email already exists 
    $email_check_sql = "SELECT * FROM users WHERE email= ?";
    $stmt = $connect->prepare($email_check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $email_check_result = $stmt->get_result();

    if (mysqli_num_rows($email_check_result) > 0) {
        $errors['email'] = "<div class='alert alert alert-danger' role='alert'>Email Already Exists</div>";
    }

    // 4. validate the phone number
    $phone_number = preg_replace('/\D/', '', $phone_number);

    if (strlen($phone_number) != 10 && strlen($phone_number) != 12) {
        $errors['phone_number'] = "<div class='alert alert-danger' role='alert'>Phone number must be 10 digits (starting with 07) or 12 digits (starting with 254).</div>";
    }

    if (strlen($phone_number) == 10 && !preg_match("/^07\d{8}$/", $phone_number)) {
        $errors['phone_number'] = "<div class='alert alert-danger' role='alert'>Invalid phone number format for Kenya (should start with 07).</div>";
    }
    if (strlen($phone_number) == 12 && !preg_match("/^254\d{9}$/", $phone_number)) {
        $errors['phone_number'] = "<div class='alert alert-danger' role='alert'>Invalid phone number format for Kenya (should start with 254).</div>";
    }

    // 5. validate the password 
    if (strlen($password) < 8) {
        $errors['password'] = "<div class='alert alert-danger' role='alert'>Password must be at least 8 characters long.</div>";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        $errors['password'] = "<div class='alert alert-danger' role='alert'>Password must contain at least one uppercase letter.</div>";
    }
    if (!preg_match("/[a-z]/", $password)) {
        $errors['password'] = "<div class='alert alert-danger' role='alert'>Password must contain at least one lowercase letter.</div>";
    }
    if (!preg_match("/[0-9]/", $password)) {
        $errors['password'] = "<div class='alert alert-danger' role='alert'>Password must contain at least one number.</div>";
    }
    if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
        $errors['password'] = "<div class='alert alert-danger' role='alert'>Password must contain at least one special character.</div>";
    }

    // 6. check if both password s match
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "<div class='alert alert-danger' role='alert'>Passwords do not match.</div>";
    }

    if (empty($errors)) {
        // 1. hash the password 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 2. insert into users based on role customer
        $role = 'Customer';

        $user_insert_sql = "INSERT INTO users (full_name, email, role, password) VALUES(?, ?, ?, ?)";
        $stmt = $connect->prepare($user_insert_sql);
        $stmt->bind_param("ssss", $full_name, $email, $role, $hashed_password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // 3. insert into customers table 
            $customer_insert_sql = "INSERT INTO customers(user_id, full_name, email, phone_number, delivery_address) VALUES(?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($customer_insert_sql);
            $stmt->bind_param("issss", $user_id, $full_name, $email, $phone_number, $delivery_address);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "<div class='alert alert-success' role='alert'>Your Account has been Created Successfully.</div>";

                if (isset($_POST['checkoutSubmit'])) {
                    header("Location: checkout.php#checkoutloginSection");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                $errors['database'] = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
            }
        } else {
            $errors['database'] = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='error-messages'>";
        foreach ($errors as $error) {
            echo "<p class='alert alert-danger'>$error</p>";
        }
        echo "</div>";
        $_SESSION['register_errors'] = $errors;
        $_SESSION['old_input'] = ['full_name' => $full_name, 'email' => $email, 'delivery_address'=>$delivery_address, 'phone_number' => $phone_number];
        if (isset($_POST['checkoutSubmit'])) {
            header("Location: checkout.php#checkoutCreateAccountSection");
            exit();
        }else {
            header("Location: index.php#createAccountSection");
            exit();
        }
    }
}
?>