<!DOCTYPE html>
<html lang="id">

<head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Toko Roti Keluarga - <?= ucfirst($page) ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="/assets/css/style.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                        <div class="container">
                                    <a class="navbar-brand" href="/">
                                                <i class="fas fa-bread-slice me-2"></i>Toko Roti Keluarga
                                    </a>
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                                <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarNav">
                                                <ul class="navbar-nav me-auto">
                                                            <li class="nav-item">
                                                                        <a class="nav-link <?= $page === 'home' ? 'active' : '' ?>" href="/">Beranda</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link <?= $page === 'products' ? 'active' : '' ?>" href="/?page=products">Produk</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link <?= $page === 'about' ? 'active' : '' ?>" href="/?page=about">Tentang Kami</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                        <a class="nav-link <?= $page === 'contact' ? 'active' : '' ?>" href="/?page=contact">Kontak</a>
                                                            </li>
                                                </ul>
                                                <ul class="navbar-nav">
                                                            <li class="nav-item">
                                                                        <a class="nav-link" href="/?page=cart">
                                                                                    <i class="fas fa-shopping-cart"></i>
                                                                                    <span class="badge bg-primary" id="cartCount">
                                                                                                <?= isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0 ?>
                                                                                    </span>
                                                                        </a>
                                                            </li>
                                                            <?php if (isLoggedIn()): ?>
                                                                        <li class="nav-item dropdown">
                                                                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                                                                                <i class="fas fa-user-circle me-1"></i>
                                                                                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                                                                                    </a>
                                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                                                <li><a class="dropdown-item" href="/?page=account">Akun Saya</a></li>
                                                                                                <?php if (isAdmin()): ?>
                                                                                                            <li><a class="dropdown-item" href="/admin/">Admin Panel</a></li>
                                                                                                <?php endif; ?>
                                                                                                <li>
                                                                                                            <hr class="dropdown-divider">
                                                                                                </li>
                                                                                                <li><a class="dropdown-item" href="/?page=logout">Logout</a></li>
                                                                                    </ul>
                                                                        </li>
                                                            <?php else: ?>
                                                                        <li class="nav-item">
                                                                                    <a class="nav-link" href="/?page=login">Login</a>
                                                                        </li>
                                                            <?php endif; ?>
                                                </ul>
                                    </div>
                        </div>
            </nav>

            <main class="container my-4">