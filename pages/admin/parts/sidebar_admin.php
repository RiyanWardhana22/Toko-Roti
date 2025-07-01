<?php
$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$site_settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
}

$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$is_settings_page = in_array($current_page, ['settings_about', 'settings_users', 'settings_slider', 'settings_website']);
?>

<nav class="admin-sidebar">
            <div class="sidebar-header">
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
                                    <a class="nav-link <?= ($current_page == 'dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= ($current_page == 'orders') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=orders">
                                                <i class="fas fa-receipt"></i> Pesanan
                                                <?php if (isset($verification_count_sidebar) && $verification_count_sidebar > 0): ?>
                                                            <span class="badge rounded-pill bg-danger ms-auto"><?= $verification_count_sidebar ?></span>
                                                <?php endif; ?>
                                    </a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= ($current_page == 'products') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=products"><i class="fas fa-box"></i> Produk</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= ($current_page == 'customers') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=customers"><i class="fas fa-users"></i> Pelanggan</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= ($current_page == 'reviews') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=reviews"><i class="fas fa-star"></i> Ulasan</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= ($current_page == 'reports') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=reports"><i class="fas fa-chart-line"></i> Laporan</a>
                        </li>
                        <li class="nav-item">
                                    <a class="nav-link <?= $is_settings_page ? 'active' : '' ?>" href="#settings-submenu" data-bs-toggle="collapse" role="button" aria-expanded="<?= $is_settings_page ? 'true' : 'false' ?>">
                                                <i class="fas fa-cog"></i> Settings
                                    </a>
                                    <div class="collapse <?= $is_settings_page ? 'show' : '' ?>" id="settings-submenu">
                                                <ul class="nav flex-column ms-3 ps-3 border-start border-2 my-1">
                                                            <li class="nav-item"><a class="nav-link py-1 <?= $current_page == 'settings_website' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_website">Website</a></li>
                                                            <li class="nav-item"><a class="nav-link py-1 <?= $current_page == 'settings_payment' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_payment">Payment</a></li>
                                                            <li class="nav-item"><a class="nav-link py-1 <?= $current_page == 'settings_slider' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_slider">Slider</a></li>
                                                            <li class="nav-item"><a class="nav-link py-1 <?= $current_page == 'settings_about' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_about">Tentang Kami</a></li>
                                                            <li class="nav-item"><a class="nav-link py-1 <?= $current_page == 'settings_users' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_users">User</a></li>
                                                </ul>
                                    </div>
                        </li>
            </ul>
</nav>