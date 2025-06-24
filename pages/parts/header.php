<?php
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!doctype html>
<html lang="id">

<head>
            <meta charset="utf-g">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Toko Roti Lezat</title>
            <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
            <nav class="navbar navbar-expand-lg bg-light">
                        <div class="container">
                                    <a class="navbar-brand" href="<?= BASE_URL ?>">Toko Roti</a>
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                                <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarNav">
                                                <ul class="navbar-nav ms-auto">
                                                            <li class="nav-item">
                                                                        <a class="nav-link" href="<?= BASE_URL ?>">Beranda</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link" href="<?= BASE_URL ?>about">Tentang Kami</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link" href="<?= BASE_URL ?>produk">Produk</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <?php
                                                                        $cart_link = isset($_SESSION['user_id']) ? BASE_URL . 'cart' : BASE_URL . 'login';
                                                                        ?>
                                                                        <a class="nav-link" href="<?= $cart_link ?>">
                                                                                    <i class="fa-solid fa-cart-shopping"></i> <span class="badge bg-danger"><?= $cart_count ?></span>
                                                                        </a>
                                                            </li>
                                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                                        <li class="nav-item dropdown">
                                                                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                                                                                    </a>
                                                                                    <ul class="dropdown-menu">
                                                                                                <li><a class="dropdown-item" href="<?= BASE_URL ?>akun">Akun Saya</a></li>
                                                                                                <li>
                                                                                                            <hr class="dropdown-divider">
                                                                                                </li>
                                                                                                <li><a class="dropdown-item" href="<?= BASE_URL ?>app/logout.php">Logout</a></li>
                                                                                    </ul>
                                                                        </li>
                                                            <?php else: ?>
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link" href="<?= BASE_URL ?>login">Login/Register</a>
                                                                        </li>
                                                            <?php endif; ?>
                                                </ul>
                                    </div>
                        </div>
            </nav>
            <main class="container py-4">