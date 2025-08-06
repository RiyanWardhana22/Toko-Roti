<?php
$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$site_settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
}

$current_page = $_GET['page'] ?? 'dashboard';

$customer_service_pages = ['customers', 'reviews', 'vouchers'];
$settings_pages = ['settings_website', 'settings_payment', 'settings_slider', 'settings_about', 'settings_users'];

$is_customer_service_page = in_array($current_page, $customer_service_pages);
$is_settings_page = in_array($current_page, $settings_pages);

/**
 * @param string 
 * @param string 
 * @return string
 */
function is_active(string $page_name, string $current_page): string
{
            return $page_name === $current_page ? 'active' : '';
}

?>

<nav class="admin-sidebar">
            <div class="sidebar-header d-flex justify-content-center">
                        <a class="navbar-brand" href="<?= BASE_URL ?>admin">
                                    <?php
                                    if (($site_settings['navbar_brand_type'] ?? 'text') == 'logo' && !empty($site_settings['navbar_brand_logo'])) {
                                                echo '<img src="' . BASE_URL . 'assets/images/' . htmlspecialchars($site_settings['navbar_brand_logo']) . '" alt="Logo Toko">';
                                    } else {
                                                echo htmlspecialchars($site_settings['navbar_brand_text'] ?? 'Toko Roti');
                                    }
                                    ?>
                        </a>
            </div>

            <ul class="nav flex-column sidebar-nav">
                        <li class="nav-item">
                                    <a class="nav-link <?= is_active('dashboard', $current_page) ?>" href="<?= BASE_URL ?>admin?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= is_active('orders', $current_page) ?>" href="<?= BASE_URL ?>admin?page=orders">
                                                <i class="fas fa-receipt"></i> Pesanan
                                                <?php if (isset($verification_count_sidebar) && $verification_count_sidebar > 0): ?>
                                                            <span class="badge rounded-pill bg-danger ms-auto"><?= $verification_count_sidebar ?></span>
                                                <?php endif; ?>
                                    </a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= is_active('categories', $current_page) ?>" href="<?= BASE_URL ?>admin?page=categories"><i class="fa-solid fa-list"></i> Kategori</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= is_active('products', $current_page) ?>" href="<?= BASE_URL ?>admin?page=products"><i class="fas fa-box"></i> Produk</a>
                        </li>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                    <li class="nav-item">
                                                <a class="nav-link <?= is_active('reports', $current_page) ?>" href="<?= BASE_URL ?>admin?page=reports"><i class="fas fa-chart-line"></i> Laporan</a>
                                    </li>

                                    <li class="nav-item">
                                                <a class="nav-link <?= $is_customer_service_page ? 'active' : '' ?>" href="#customer-service-submenu" data-bs-toggle="collapse" role="button" aria-expanded="<?= $is_customer_service_page ? 'true' : 'false' ?>">
                                                            <i class="fa-solid fa-address-card"></i> Layanan Pelanggan
                                                </a>
                                                <div class="collapse <?= $is_customer_service_page ? 'show' : '' ?>" id="customer-service-submenu">
                                                            <ul class="nav flex-column ms-3 ps-3 border-start border-2 my-1">
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('customers', $current_page) ?>" href="<?= BASE_URL ?>admin?page=customers">Pelanggan</a></li>
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('reviews', $current_page) ?>" href="<?= BASE_URL ?>admin?page=reviews">Ulasan</a></li>
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('vouchers', $current_page) ?>" href="<?= BASE_URL ?>admin?page=vouchers">Voucher</a></li>
                                                            </ul>
                                                </div>
                                    </li>

                                    <li class="nav-item">
                                                <a class="nav-link <?= $is_settings_page ? 'active' : '' ?>" href="#settings-submenu" data-bs-toggle="collapse" role="button" aria-expanded="<?= $is_settings_page ? 'true' : 'false' ?>">
                                                            <i class="fas fa-cog"></i> Settings
                                                </a>
                                                <div class="collapse <?= $is_settings_page ? 'show' : '' ?>" id="settings-submenu">
                                                            <ul class="nav flex-column ms-3 ps-3 border-start border-2 my-1">
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('settings_website', $current_page) ?>" href="<?= BASE_URL ?>admin?page=settings_website">Website</a></li>
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('settings_payment', $current_page) ?>" href="<?= BASE_URL ?>admin?page=settings_payment">Payment</a></li>
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('settings_slider', $current_page) ?>" href="<?= BASE_URL ?>admin?page=settings_slider">Slider</a></li>
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('settings_about', $current_page) ?>" href="<?= BASE_URL ?>admin?page=settings_about">Tentang Kami</a></li>
                                                                        <li class="nav-item"><a class="nav-link py-1 <?= is_active('settings_users', $current_page) ?>" href="<?= BASE_URL ?>admin?page=settings_users">User</a></li>
                                                            </ul>
                                                </div>
                                    </li>
                        <?php endif ?>
            </ul>
</nav>