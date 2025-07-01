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
            <nav class="navbar navbar-expand-lg sticky-top">
                        <div class="container">
                                    <?php
                                    $brand_type = $site_settings['navbar_brand_type'] ?? 'text';
                                    if ($brand_type == 'logo' && !empty($site_settings['navbar_brand_logo'])) {
                                                echo '<a class="navbar-brand" href="' . BASE_URL . '"><img src="' . BASE_URL . 'assets/images/' . htmlspecialchars($site_settings['navbar_brand_logo']) . '" alt="Logo Toko" style="height: 40px;"></a>';
                                    } else {
                                                echo '<a class="navbar-brand" href="' . BASE_URL . '">' . htmlspecialchars($site_settings['navbar_brand_text'] ?? 'Toko Roti') . '</a>';
                                    }
                                    ?>
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                                <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarNav">
                                                <ul class="navbar-nav mx-auto">
                                                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>">Beranda</a></li>
                                                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>about">Tentang Kami</a></li>
                                                            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>produk">Produk</a></li>
                                                </ul>
                                                <ul class="navbar-nav">
                                                            <li class="nav-item">
                                                                        <?php $cart_link = isset($_SESSION['user_id']) ? BASE_URL . 'cart' : BASE_URL . 'login'; ?>
                                                                        <a class="nav-link" href="<?= $cart_link ?>">
                                                                                    <i class="fas fa-shopping-cart"></i> <span class="badge bg-danger rounded-pill"><?= $cart_count ?></span>
                                                                        </a>
                                                            </li>
                                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                                        <li class="nav-item dropdown">
                                                                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                                                                                    </a>
                                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                                                                                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin">Dashboard</a></li>
                                                                                                <?php endif; ?>
                                                                                                <li><a class="dropdown-item" href="<?= BASE_URL ?>akun">Akun Saya</a></li>
                                                                                                <li>
                                                                                                            <hr class="dropdown-divider">
                                                                                                </li>
                                                                                                <li><a class="dropdown-item" href="<?= BASE_URL ?>app/logout.php">Logout</a></li>
                                                                                    </ul>
                                                                        </li>
                                                            <?php else: ?>
                                                                        <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>login">Login</a></li>
                                                            <?php endif; ?>
                                                </ul>
                                    </div>
                        </div>
            </nav>

            <script>
                        document.addEventListener("DOMContentLoaded", function() {
                                    const navbar = document.querySelector('.navbar');
                                    if (navbar) {
                                                window.addEventListener('scroll', function() {
                                                            if (window.scrollY > 50) {
                                                                        navbar.classList.add('scrolled');
                                                            } else {
                                                                        navbar.classList.remove('scrolled');
                                                            }
                                                });
                                    }
                        });
            </script>