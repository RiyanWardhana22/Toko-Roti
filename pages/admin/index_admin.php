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
$allowed_admin_pages = ['dashboard', 'products', 'orders'];

include 'parts/header_admin.php';
include 'parts/sidebar_admin.php';

echo '<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">';

if (in_array($admin_page, $allowed_admin_pages) && file_exists($admin_page . '.php')) {
            include $admin_page . '.php';
} else {
            include 'dashboard.php';
}

echo '</div>';

include 'parts/footer_admin.php';
