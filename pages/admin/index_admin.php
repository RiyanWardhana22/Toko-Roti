<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

$user_id = $_SESSION['user_id'];
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_res);
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'pegawai'])) {
            header('Location: ' . BASE_URL);
            exit();
}

$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$site_settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
}

$verification_res_sidebar = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE status = 'Menunggu Verifikasi'");
$verification_count_sidebar = mysqli_fetch_assoc($verification_res_sidebar)['total'];

$admin_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$allowed_admin_pages = ['dashboard', 'products', 'orders', 'customers', 'categories', 'vouchers', 'reviews', 'reports', 'invoice', 'settings_about', 'settings_slider', 'settings_users', 'settings_website', 'settings_payment'];
$admin_standalone_pages = ['invoice'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard - <?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti') ?></title>
            <?php if (!empty($site_settings['website_favicon'])): ?>
                        <link rel="icon" href="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['website_favicon']) ?>">
            <?php endif; ?>
            <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
            <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin_style.css">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="admin-body">

            <?php
            if (in_array($admin_page, $admin_standalone_pages)) {
                        if (file_exists(__DIR__ . '/' . $admin_page . '.php')) {
                                    include __DIR__ . '/' . $admin_page . '.php';
                        } else {
                                    echo "404 - Halaman tidak ditemukan";
                        }
            } else {
            ?>
                        <div class="admin-wrapper">
                                    <?php include 'parts/sidebar_admin.php'; ?>
                                    <div class="main-content">
                                                <?php include 'parts/header_admin.php'; ?>
                                                <main class="page-content">
                                                            <?php
                                                            $page_path = __DIR__ . '/' . $admin_page . '.php';
                                                            if (in_array($admin_page, $allowed_admin_pages) && file_exists($page_path)) {
                                                                        include $page_path;
                                                            } else {
                                                                        include __DIR__ . '/dashboard.php';
                                                            }
                                                            ?>
                                                </main>
                                    </div>
                        </div>
            <?php
            }
            ?>

            <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
            <script>
                        const sidebar = document.querySelector('.admin-sidebar');
                        const mobileToggler = document.querySelector('.mobile-toggler');
                        mobileToggler.addEventListener('click', function() {
                                    sidebar.classList.toggle('active');
                        });

                        document.addEventListener('click', function(event) {
                                    const isClickInsideSidebar = sidebar.contains(event.target);
                                    const isClickOnToggler = mobileToggler.contains(event.target);
                                    if (sidebar.classList.contains('active') && !isClickInsideSidebar && !isClickOnToggler) {
                                                sidebar.classList.remove('active');
                                    }
                        });
            </script>
</body>

</html>