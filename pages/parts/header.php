<?php
$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$site_settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!doctype html>
<html lang="id">

<head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti Lezat') ?></title>
            <?php if (!empty($site_settings['website_favicon'])): ?>
                        <link rel="icon" href="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['website_favicon']) ?>">
            <?php endif; ?>

            <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
            <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom_style.css">
            <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/akun.css">
</head>

<body>
            <header class="site-header">
                        <div class="header-container">
                                    <div class="header-brand">
                                                <?php
                                                $brand_type = $site_settings['navbar_brand_type'] ?? 'text';
                                                if ($brand_type == 'logo' && !empty($site_settings['navbar_brand_logo'])) {
                                                            echo '<a href="' . BASE_URL . '" class="brand-logo"><img src="' . BASE_URL . 'assets/images/' . htmlspecialchars($site_settings['navbar_brand_logo']) . '" alt="Logo Toko"></a>';
                                                } else {
                                                            echo '<a href="' . BASE_URL . '" class="brand-text">' . htmlspecialchars($site_settings['navbar_brand_text'] ?? 'Toko Roti') . '</a>';
                                                }
                                                ?>
                                    </div>

                                    <button class="mobile-menu-toggle" aria-label="Toggle navigation">
                                                <span class="toggle-bar"></span>
                                                <span class="toggle-bar"></span>
                                                <span class="toggle-bar"></span>
                                    </button>

                                    <nav class="main-navigation">
                                                <ul class="nav-menu">
                                                            <li class="nav-item"><a href="<?= BASE_URL ?>" class="nav-link">Beranda</a></li>
                                                            <li class="nav-item"><a href="<?= BASE_URL ?>about" class="nav-link">Tentang Kami</a></li>
                                                            <li class="nav-item"><a href="<?= BASE_URL ?>produk" class="nav-link">Produk</a></li>
                                                </ul>

                                                <div class="header-actions">
                                                            <?php $cart_link = isset($_SESSION['user_id']) ? BASE_URL . 'cart' : BASE_URL . 'login'; ?>
                                                            <a href="<?= $cart_link ?>" class="cart-icon" aria-label="Cart">
                                                                        <i class="fas fa-shopping-cart"></i>
                                                                        <?php if ($cart_count > 0): ?>
                                                                                    <span class="cart-count"><?= $cart_count ?></span>
                                                                        <?php endif; ?>
                                                            </a>

                                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                                        <div class="user-dropdown">
                                                                                    <button class="user-profile" aria-expanded="false" aria-label="User menu">
                                                                                                <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                                                                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu">
                                                                                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                                                                                            <li><a href="<?= BASE_URL ?>admin" class="dropdown-item"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                                                                                <?php endif; ?>
                                                                                                <li><a href="<?= BASE_URL ?>akun" class="dropdown-item"><i class="fas fa-user me-2"></i>Akun Saya</a></li>
                                                                                                <li class="dropdown-divider"></li>
                                                                                                <li><a href="<?= BASE_URL ?>app/logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                                                                    </ul>
                                                                        </div>
                                                            <?php else: ?>
                                                                        <a href="<?= BASE_URL ?>login" class="login-btn">Login</a>
                                                            <?php endif; ?>
                                                </div>
                                    </nav>
                        </div>
            </header>

            <script>
                        document.addEventListener('DOMContentLoaded', function() {
                                    const mobileToggle = document.querySelector('.mobile-menu-toggle');
                                    const navigation = document.querySelector('.main-navigation');

                                    if (mobileToggle && navigation) {
                                                mobileToggle.addEventListener('click', function(e) {
                                                            e.stopPropagation();
                                                            this.classList.toggle('active');
                                                            navigation.classList.toggle('active');
                                                            document.body.style.overflow = navigation.classList.contains('active') ? 'hidden' : '';
                                                });

                                                document.addEventListener('click', function(e) {
                                                            if (!navigation.contains(e.target) && !mobileToggle.contains(e.target)) {
                                                                        mobileToggle.classList.remove('active');
                                                                        navigation.classList.remove('active');
                                                                        document.body.style.overflow = '';
                                                            }
                                                });

                                                const navLinks = document.querySelectorAll('.nav-link, .dropdown-item');
                                                navLinks.forEach(link => {
                                                            link.addEventListener('click', function() {
                                                                        if (window.innerWidth <= 992) {
                                                                                    mobileToggle.classList.remove('active');
                                                                                    navigation.classList.remove('active');
                                                                                    document.body.style.overflow = '';
                                                                        }
                                                            });
                                                });
                                    } else {
                                                console.error('Elemen menu tidak ditemukan!');
                                    }
                        });
            </script>