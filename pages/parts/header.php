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
                                                                        <a class="nav-link" href="<?= BASE_URL ?>produk">Produk</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link" href="<?= BASE_URL ?>keranjang">
                                                                                    Keranjang <span class="badge bg-danger"><?= $cart_count ?></span>
                                                                        </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link" href="<?= BASE_URL ?>akun">Akun Saya</a>
                                                            </li>
                                                </ul>
                                    </div>
                        </div>
            </nav>
            <main class="container py-4">