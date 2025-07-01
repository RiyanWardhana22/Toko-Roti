<?php
require_once 'config.php';
require_once 'mailer.php';

session_start();

if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed.');
}

if (!isset($_POST['action'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$action = $_POST['action'];

if ($action == 'register') {
            $errors = [];
            $input = $_POST;

            if (empty($input['name'])) {
                        $errors['name'] = 'Nama lengkap wajib diisi.';
            }
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors['email'] = 'Format email tidak valid.';
            }
            if (strlen($input['password']) < 8) {
                        $errors['password'] = 'Password minimal harus 8 karakter.';
            }

            if (empty($errors['email'])) {
                        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
                        mysqli_stmt_bind_param($stmt, 's', $input['email']);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                        if (mysqli_stmt_num_rows($stmt) > 0) {
                                    $errors['email'] = 'Email ini sudah terdaftar.';
                        }
                        mysqli_stmt_close($stmt);
            }

            if (!empty($errors)) {
                        $_SESSION['errors'] = $errors;
                        $_SESSION['old'] = $input;
                        header('Location: ' . BASE_URL . 'register');
                        exit();
            }

            $password_hashed = password_hash($input['password'], PASSWORD_DEFAULT);
            $role = 'customer';

            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, address, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssssss', $input['name'], $input['email'], $input['phone'], $input['address'], $password_hashed, $role);

            if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['user_id'] = mysqli_insert_id($conn);
                        $_SESSION['user_name'] = $input['name'];
                        $_SESSION['user_role'] = $role;

                        unset($_SESSION['errors'], $_SESSION['old']);

                        $settings_res = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'website_title'");
                        $website_name = mysqli_fetch_assoc($settings_res)['setting_value'] ?? 'Toko Roti Anda';
                        $template_path = __DIR__ . '/../templates/email/welcome_email_template.html';

                        if (file_exists($template_path)) {
                                    $email_body = file_get_contents($template_path);
                                    $email_body = str_replace('{{customer_name}}', $input['name'], $email_body);
                                    $email_body = str_replace('{{website_name}}', $website_name, $email_body);
                                    $email_body = str_replace('{{base_url}}', BASE_URL, $email_body);
                                    $email_body = str_replace('{{current_year}}', date('Y'), $email_body);

                                    $subject = "Selamat Datang di " . $website_name;
                                    send_email($input['email'], $input['name'], $subject, $email_body);
                        }

                        header('Location: ' . BASE_URL . 'akun');
                        exit();
            } else {
                        $_SESSION['errors'] = ['generic' => 'Terjadi kesalahan pada server. Silakan coba lagi.'];
                        $_SESSION['old'] = $input;
                        header('Location: ' . BASE_URL . 'register');
                        exit();
            }
            mysqli_stmt_close($stmt);
} elseif ($action == 'login') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = mysqli_prepare($conn, "SELECT id, name, email, password, role FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($user = mysqli_fetch_assoc($result)) {
                        if (password_verify($password, $user['password'])) {
                                    $_SESSION['user_id'] = $user['id'];
                                    $_SESSION['user_name'] = $user['name'];
                                    $_SESSION['user_role'] = $user['role'];

                                    unset($_SESSION['errors'], $_SESSION['old']);

                                    header('Location: ' . BASE_URL);
                                    exit();
                        }
            }

            $_SESSION['errors'] = ['generic' => 'Email atau password salah.'];
            $_SESSION['old'] = ['email' => $email];
            header('Location: ' . BASE_URL . 'login');
            exit();
}
