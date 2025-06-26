<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT role FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);

if (!$user || $user['role'] !== 'admin') {
            header('Location: ' . BASE_URL);
            exit();
}

$admin_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$allowed_admin_pages = [
            'dashboard',
            'products',
            'orders',
            'customers',
            'categories',
            'vouchers',
            'reports',
            'invoice',
            'settings_about',
            'settings_slider',
            'settings_users',
            'settings_website',
            'settings_payment'
];

$admin_standalone_pages = ['invoice'];
if (in_array($admin_page, $admin_standalone_pages)) {
            if (file_exists(__DIR__ . '/' . $admin_page . '.php')) {
                        include __DIR__ . '/' . $admin_page . '.php';
            } else {
                        echo "404 - Halaman tidak ditemukan";
            }
} else {
            $verification_res_sidebar = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE status = 'Menunggu Verifikasi'");
            $verification_count_sidebar = mysqli_fetch_assoc($verification_res_sidebar)['total'];
            include 'parts/header_admin.php';
            include 'parts/sidebar_admin.php';

            echo '<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">';
            $page_path = __DIR__ . '/' . $admin_page . '.php';

            if (in_array($admin_page, $allowed_admin_pages) && file_exists($page_path)) {
                        include $page_path;
            } else {
                        include __DIR__ . '/dashboard.php';
            }

            echo '</div>';
            include 'parts/footer_admin.php';
}
