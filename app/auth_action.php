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
} elseif ($action == 'request_reset') {
            $email = $_POST['email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $_SESSION['errors'] = ['email' => 'Format email tidak valid.'];
                        $_SESSION['old'] = ['email' => $email];
                        header('Location: ' . BASE_URL . 'forgot_password');
                        exit();
            }

            $stmt = mysqli_prepare($conn, "SELECT id, name FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user) {
                        $otp = random_int(10000000, 99999999);
                        $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $stmt_update = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt_update, 'ssi', $otp, $expires_at, $user['id']);
                        mysqli_stmt_execute($stmt_update);
                        mysqli_stmt_close($stmt_update);

                        $settings_res = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'website_title'");
                        $website_name = mysqli_fetch_assoc($settings_res)['setting_value'] ?? 'Toko Roti Anda';

                        $subject = "Kode Reset Password untuk " . $website_name;
                        $email_body = "
            <h2>Reset Password Anda</h2>
            <p>Halo " . htmlspecialchars($user['name']) . ",</p>
            <p>Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan. Kode ini hanya berlaku selama 15 menit.</p>
            <h3 style='text-align:center; letter-spacing: 5px; font-size: 28px; background-color: #f2f2f2; padding: 15px;'>$otp</h3>
            <p>Jika Anda tidak merasa meminta reset password, silakan abaikan email ini.</p>
            <p>Terima kasih,<br>Tim $website_name</p>
        ";

                        send_email($email, $user['name'], $subject, $email_body);
                        $_SESSION['reset_email'] = $email;
                        header('Location: ' . BASE_URL . 'verify_otp');
                        exit();
            } else {
                        $_SESSION['errors'] = ['generic' => 'Email yang Anda masukkan tidak terdaftar.'];
                        $_SESSION['old'] = ['email' => $email];
                        header('Location: ' . BASE_URL . 'forgot_password');
                        exit();
            }
} elseif ($action == 'verify_otp') {
            if (!isset($_SESSION['reset_email'])) {
                        header('Location: ' . BASE_URL . 'login');
                        exit();
            }

            $otp = $_POST['otp'];
            $email = $_SESSION['reset_email'];

            $stmt = mysqli_prepare($conn, "SELECT reset_token, reset_token_expires_at FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user && $user['reset_token'] == $otp && strtotime($user['reset_token_expires_at']) > time()) {
                        $_SESSION['otp_verified'] = true;
                        $stmt_clear = mysqli_prepare($conn, "UPDATE users SET reset_token = NULL, reset_token_expires_at = NULL WHERE email = ?");
                        mysqli_stmt_bind_param($stmt_clear, 's', $email);
                        mysqli_stmt_execute($stmt_clear);
                        mysqli_stmt_close($stmt_clear);

                        header('Location: ' . BASE_URL . 'reset_password');
                        exit();
            } else {
                        $_SESSION['errors'] = ['generic' => 'Kode OTP yang Anda masukkan salah atau sudah kedaluwarsa.'];
                        header('Location: ' . BASE_URL . 'verify_otp');
                        exit();
            }
} elseif ($action == 'reset_password') {
            if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
                        header('Location: ' . BASE_URL . 'login');
                        exit();
            }

            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $email = $_SESSION['reset_email'];

            if (strlen($password) < 8) {
                        $_SESSION['errors'] = ['password' => 'Password minimal harus 8 karakter.'];
                        header('Location: ' . BASE_URL . 'reset_password');
                        exit();
            }
            if ($password !== $confirm_password) {
                        $_SESSION['errors'] = ['generic' => 'Konfirmasi password tidak cocok.'];
                        header('Location: ' . BASE_URL . 'reset_password');
                        exit();
            }

            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $password_hashed, $email);

            if (mysqli_stmt_execute($stmt)) {
                        unset($_SESSION['reset_email'], $_SESSION['otp_verified']);
                        $_SESSION['success_message'] = 'Password Anda berhasil diubah! Silakan login dengan password baru Anda.';
                        header('Location: ' . BASE_URL . 'login');
                        exit();
            } else {
                        $_SESSION['errors'] = ['generic' => 'Terjadi kesalahan saat mengubah password. Silakan coba lagi.'];
                        header('Location: ' . BASE_URL . 'reset_password');
                        exit();
            }
            mysqli_stmt_close($stmt);
}
