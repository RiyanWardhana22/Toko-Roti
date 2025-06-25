<?php
require_once 'config.php';
require_once 'mailer.php';

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
                        $_SESSION['user_role'] = 'customer';
                        $settings_res = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'website_title'");
                        $website_name = mysqli_fetch_assoc($settings_res)['setting_value'] ?? 'Toko Roti Anda';

                        $template_path = __DIR__ . '/../templates/email/welcome_email_template.html';
                        if (file_exists($template_path)) {
                                    $email_body = file_get_contents($template_path);
                                    $email_body = str_replace('{{customer_name}}', $name, $email_body);
                                    $email_body = str_replace('{{website_name}}', $website_name, $email_body);
                                    $email_body = str_replace('{{base_url}}', BASE_URL, $email_body);
                                    $email_body = str_replace('{{current_year}}', date('Y'), $email_body);

                                    $subject = "Selamat Datang di " . $website_name;
                                    send_email($email, $name, $subject, $email_body);
                        }
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
                                    $_SESSION['user_role'] = $user['role'];
                                    header('Location: ' . BASE_URL);
                                    exit();
                        }
            }
            header('Location: ' . BASE_URL . 'login?error=invalid_credentials');
            exit();
}
