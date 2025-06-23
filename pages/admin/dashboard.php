<?php
$today = date('Y-m-d');
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'Selesai' AND DATE(created_at) = '$today'"))['total'] ?? 0;
$pesanan_baru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE DATE(created_at) = '$today'"))['total'];
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role = 'customer'"))['total'];
$total_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM products"))['total'];

$result_pesanan_terbaru = mysqli_query($conn, "SELECT id, user_id, total_amount, status, created_at FROM orders ORDER BY created_at DESC LIMIT 5");

$result_stok_menipis = mysqli_query($conn, "SELECT id, name, stock FROM products WHERE stock < 5 AND stock > 0");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard Utama</h1>
</div>

<div class="row">
            <div class="col-md-3">
                        <div class="card text-bg-primary mb-3">
                                    <div class="card-header">Pendapatan Hari Ini</div>
                                    <div class="card-body">
                                                <h5 class="card-title">Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></h5>
                                    </div>
                        </div>
            </div>
            <div class="col-md-3">
                        <div class="card text-bg-success mb-3">
                                    <div class="card-header">Pesanan Baru Hari Ini</div>
                                    <div class="card-body">
                                                <h5 class="card-title"><?= $pesanan_baru ?></h5>
                                    </div>
                        </div>
            </div>
            <div class="col-md-3">
                        <div class="card text-bg-info mb-3">
                                    <div class="card-header">Total Pelanggan</div>
                                    <div class="card-body">
                                                <h5 class="card-title"><?= $total_pelanggan ?></h5>
                                    </div>
                        </div>
            </div>
            <div class="col-md-3">
                        <div class="card text-bg-secondary mb-3">
                                    <div class="card-header">Total Produk</div>
                                    <div class="card-body">
                                                <h5 class="card-title"><?= $total_produk ?></h5>
                                    </div>
                        </div>
            </div>
</div>

<?php if (mysqli_num_rows($result_stok_menipis) > 0): ?>
            <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Stok produk berikut menipis:
                        <ul>
                                    <?php while ($produk = mysqli_fetch_assoc($result_stok_menipis)): ?>
                                                <li><?= htmlspecialchars($produk['name']) ?> (Sisa: <?= $produk['stock'] ?>)</li>
                                    <?php endwhile; ?>
                        </ul>
            </div>
<?php endif; ?>

<div class="row">
            <div class="col-md-12">
                        <h2>Pesanan Terbaru</h2>
                        <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                                <thead>
                                                            <tr>
                                                                        <th>ID Pesanan</th>
                                                                        <th>Total</th>
                                                                        <th>Status</th>
                                                                        <th>Tanggal</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan_terbaru)): ?>
                                                                        <tr>
                                                                                    <td>#<?= $pesanan['id'] ?></td>
                                                                                    <td>Rp <?= number_format($pesanan['total_amount'], 0, ',', '.') ?></td>
                                                                                    <td><span class="badge bg-info"><?= $pesanan['status'] ?></span></td>
                                                                                    <td><?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?></td>
                                                                        </tr>
                                                            <?php endwhile; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>
</div>