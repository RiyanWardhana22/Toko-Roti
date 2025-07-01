<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<main>
            <div class=" container py-5">
                        <div class="row g-4">
                                    <div class="col-lg-3">
                                                <div class="list-group account-nav">
                                                            <a href="<?= BASE_URL ?>akun?tab=dashboard" class="list-group-item list-group-item-action <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
                                                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                                            </a>
                                                            <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="list-group-item list-group-item-action <?= $active_tab == 'riwayat_pesanan' ? 'active' : '' ?>">
                                                                        <i class="fas fa-receipt"></i> Riwayat Pesanan
                                                            </a>
                                                            <a href="<?= BASE_URL ?>akun?tab=profil" class="list-group-item list-group-item-action <?= $active_tab == 'profil' ? 'active' : '' ?>">
                                                                        <i class="fa-solid fa-user"></i> Profil Saya
                                                            </a>
                                                            <a href="<?= BASE_URL ?>akun?tab=ubah_password" class="list-group-item list-group-item-action <?= $active_tab == 'ubah_password' ? 'active' : '' ?>">
                                                                        <i class="fas fa-key"></i> Ubah Password
                                                            </a>
                                                            <a href="<?= BASE_URL ?>app/logout.php" class="list-group-item list-group-item-action text-danger">
                                                                        <i class="fas fa-sign-out-alt"></i> Logout
                                                            </a>
                                                </div>
                                    </div>

                                    <div class="col-lg-9">
                                                <div class="card account-card">
                                                            <div class="card-body">
                                                                        <?php
                                                                        // Logika switch tidak berubah
                                                                        switch ($active_tab) {
                                                                                    case 'riwayat_pesanan':
                                                                                                include 'parts/account/order_history.php';
                                                                                                break;
                                                                                    case 'profil':
                                                                                                include 'parts/account/profile.php';
                                                                                                break;
                                                                                    case 'ubah_password':
                                                                                                include 'parts/account/change_password.php';
                                                                                                break;
                                                                                    case 'dashboard':
                                                                                    default:
                                                                                                include 'parts/account/dashboard.php';
                                                                                                break;
                                                                        }
                                                                        ?>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>