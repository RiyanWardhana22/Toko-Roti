<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<div class="container py-4">
            <div class="row">
                        <div class="col-md-3">
                                    <div class="list-group">
                                                <a href="<?= BASE_URL ?>akun?tab=dashboard" class="list-group-item list-group-item-action <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
                                                            Dashboard
                                                </a>
                                                <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="list-group-item list-group-item-action <?= $active_tab == 'riwayat_pesanan' ? 'active' : '' ?>">
                                                            Riwayat Pesanan
                                                </a>
                                                <a href="<?= BASE_URL ?>akun?tab=profil" class="list-group-item list-group-item-action <?= $active_tab == 'profil' ? 'active' : '' ?>">
                                                            Profil Saya
                                                </a>
                                                <a href="<?= BASE_URL ?>akun?tab=ubah_password" class="list-group-item list-group-item-action <?= $active_tab == 'ubah_password' ? 'active' : '' ?>">
                                                            Ubah Password
                                                </a>
                                                <a href="<?= BASE_URL ?>app/logout.php" class="list-group-item list-group-item-action text-danger">
                                                            Logout
                                                </a>
                                    </div>
                        </div>

                        <div class="col-md-9">
                                    <div class="card">
                                                <div class="card-body">
                                                            <?php
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