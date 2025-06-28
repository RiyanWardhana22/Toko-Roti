<?php
$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$site_settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
}

$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$is_settings_page = in_array($current_page, ['settings_about', 'settings_users', 'settings_slider', 'settings_website']);
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3 sidebar-sticky">

                        <div class="px-3 mb-2">
                                    <?php
                                    $brand_type = $site_settings['navbar_brand_type'] ?? 'text';
                                    if ($brand_type == 'logo' && !empty($site_settings['navbar_brand_logo'])) {
                                                echo '<a href="' . BASE_URL . '"><img src="' . BASE_URL . 'assets/images/' . htmlspecialchars($site_settings['navbar_brand_logo']) . '" alt="Logo Toko" style="height: 40px; max-width: 100%;"></a>';
                                    } else {
                                                $brand_text = $site_settings['navbar_brand_text'] ?? 'Toko Roti';
                                                echo '<a class="navbar-brand fs-6 text-dark text-uppercase" href="' . BASE_URL . 'admin">' . htmlspecialchars($brand_text) . '</a>';
                                    }
                                    ?>
                        </div>

                        <ul class="nav flex-column">
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=dashboard">Dashboard</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'orders') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=orders">
                                                            Pesanan
                                                            <?php if (isset($verification_count_sidebar) && $verification_count_sidebar > 0): ?>
                                                                        <span class="badge rounded-pill text-bg-danger ms-1"><?= $verification_count_sidebar ?></span>
                                                            <?php endif; ?>
                                                </a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'products') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=products">Produk</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'customers') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=customers">Pelanggan</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'categories') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=categories">Kategori</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'vouchers') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=vouchers">Voucher</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'reviews') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=reviews">Ulasan</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= ($current_page == 'reports') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=reports">Laporan</a>
                                    </li>
                                    <li class="nav-item">
                                                <a class="nav-link <?= $is_settings_page ? 'active' : '' ?>" href="#submenu-settings" data-bs-toggle="collapse" role="button" aria-expanded="<?= $is_settings_page ? 'true' : 'false' ?>" aria-controls="submenu-settings">
                                                            Settings
                                                </a>
                                                <div class="collapse <?= $is_settings_page ? 'show' : '' ?>" id="submenu-settings">
                                                            <ul class="nav flex-column ms-3">
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link <?= $current_page == 'settings_website' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_website">Website</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link <?= $current_page == 'settings_slider' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_slider">Slider Homepage</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link <?= $current_page == 'settings_about' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_about">Tentang Kami</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link <?= $current_page == 'settings_users' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_users">User</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link <?= $current_page == 'settings_payment' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin?page=settings_payment">Payment</a>
                                                                        </li>
                                                            </ul>
                                                </div>
                                    </li>
                        </ul>
            </div>
</nav>