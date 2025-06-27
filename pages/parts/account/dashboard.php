<?php
$user_id = $_SESSION['user_id'];

$stats_res = mysqli_query($conn, "SELECT COUNT(id) as total_orders, SUM(total_amount) as total_spent FROM orders WHERE user_id = $user_id AND status = 'Selesai'");
$stats = mysqli_fetch_assoc($stats_res);
?>

<h3 class="card-title mb-4">Dashboard</h3>
<p class="lead">Selamat Datang kembali, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</p>
<p class="text-muted">Dari dasbor akun Anda, Anda dapat melihat pesanan terakhir, mengelola alamat, dan mengedit detail akun Anda.</p>
<hr>
<div class="row g-4 my-3">
            <div class="col-md-6">
                        <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-box-open"></i></div>
                                    <div class="stat-title">Total Pesanan Selesai</div>
                                    <div class="stat-value"><?= (int)($stats['total_orders'] ?? 0) ?></div>
                        </div>
            </div>
            <div class="col-md-6">
                        <div class="stat-card">
                                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                                    <div class="stat-title">Total Belanja</div>
                                    <div class="stat-value">Rp <?= number_format($stats['total_spent'] ?? 0, 0, ',', '.') ?></div>
                        </div>
            </div>
</div>