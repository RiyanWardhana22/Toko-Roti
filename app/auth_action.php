<?php
require_once 'config.php';

if (!isset($_POST['action'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$action = $_POST['action'];

if ($action == 'register') {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $address = mysqli_real_escape_string($conn, $_POST['address']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
            if (mysqli_num_rows($check_email) > 0) {
                        header('Location: ' . BASE_URL . 'register?error=email_exists');
                        exit();
            }

            $query = "INSERT INTO users (name, email, phone, address, password, role) VALUES ('$name', '$email', '$phone', '$address', '$password', 'customer')";
            if (mysqli_query($conn, $query)) {
                        $user_id = mysqli_insert_id($conn);
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_name'] = $name;
                        header('Location: ' . BASE_URL . 'akun');
                        exit();
            }
} elseif ($action == 'login') {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];

            $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
            if (mysqli_num_rows($result) === 1) {
                        $user = mysqli_fetch_assoc($result);
                        if (password_verify($password, $user['password'])) {
                                    $_SESSION['user_id'] = $user['id'];
                                    $_SESSION['user_name'] = $user['name'];
                                    header('Location: ' . BASE_URL . 'checkout');
                                    exit();
                        }
            }
            header('Location: ' . BASE_URL . 'login?error=invalid_credentials');
            exit();
}
