<?php
$verification_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE status = 'Menunggu Verifikasi'");
$verification_count = mysqli_fetch_assoc($verification_res)['total'];

$today = date('Y-m-d');
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'Selesai' AND DATE(created_at) = '$today'"))['total'] ?? 0;
$pesanan_baru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE DATE(created_at) = '$today'"))['total'];
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role = 'customer'"))['total'];
$total_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM products"))['total'];

$result_pesanan_terbaru = mysqli_query($conn, "SELECT o.id, u.name as customer_name, o.total_amount, o.status, o.created_at FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$result_stok_menipis = mysqli_query($conn, "SELECT id, name, stock FROM products WHERE stock < 10 AND stock > 0");
?>

<?php if ($verification_count > 0): ?>
            <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                                    <h5 class="alert-heading mb-1">Ada Konfirmasi Pembayaran Baru!</h5>
                                    Anda memiliki <strong><?= $verification_count ?> pesanan</strong> yang menunggu untuk diverifikasi.
                                    <a href="<?= BASE_URL ?>admin?page=orders&status=Menunggu+Verifikasi" class="ms-2 fw-bold">Lihat Sekarang</a>
                        </div>
            </div>
<?php endif; ?>

<div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card content-card h-100">
                                    <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                            <div class="me-3"><i class="fas fa-dollar-sign fa-2x text-success"></i></div>
                                                            <div>
                                                                        <p class="text-muted mb-0">Pendapatan Hari Ini</p>
                                                                        <h5 class="mb-0">Rp <?= number_format($pendapatan_hari_ini) ?></h5>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card content-card h-100">
                                    <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                            <div class="me-3"><i class="fas fa-shopping-cart fa-2x text-primary"></i></div>
                                                            <div>
                                                                        <p class="text-muted mb-0">Pesanan Baru Hari Ini</p>
                                                                        <h5 class="mb-0"><?= $pesanan_baru ?></h5>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card content-card h-100">
                                    <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                            <div class="me-3"><i class="fas fa-users fa-2x text-info"></i></div>
                                                            <div>
                                                                        <p class="text-muted mb-0">Total Pelanggan</p>
                                                                        <h5 class="mb-0"><?= $total_pelanggan ?></h5>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card content-card h-100">
                                    <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                            <div class="me-3"><i class="fas fa-box-open fa-2x text-secondary"></i></div>
                                                            <div>
                                                                        <p class="text-muted mb-0">Total Produk</p>
                                                                        <h5 class="mb-0"><?= $total_produk ?></h5>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>

<div class="row">
            <div class="col-md-8">
                        <div class="card content-card">
                                    <div class="card-header">
                                                Pesanan Terbaru
                                    </div>
                                    <div class="card-body">
                                                <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                        <thead>
                                                                                    <tr>
                                                                                                <th>ID</th>
                                                                                                <th>Pelanggan</th>
                                                                                                <th>Total</th>
                                                                                                <th>Status</th>
                                                                                                <th>Tanggal</th>
                                                                                    </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan_terbaru)): ?>
                                                                                                <tr>
                                                                                                            <td><a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $pesanan['id'] ?>">#<?= $pesanan['id'] ?></a></td>
                                                                                                            <td><?= htmlspecialchars($pesanan['customer_name']) ?></td>
                                                                                                            <td>Rp <?= number_format($pesanan['total_amount']) ?></td>
                                                                                                            <td><span class="badge rounded-pill text-bg-info"><?= $pesanan['status'] ?></span></td>
                                                                                                            <td><?= date('d M Y', strtotime($pesanan['created_at'])) ?></td>
                                                                                                </tr>
                                                                                    <?php endwhile; ?>
                                                                        </tbody>
                                                            </table>
                                                </div>
                                    </div>
                        </div>
            </div>
            <div class="col-md-4">
                        <div class="card content-card">
                                    <div class="card-header">
                                                <i class="fas fa-exclamation-triangle text-warning me-2"></i> Stok Menipis
                                    </div>
                                    <div class="card-body">
                                                <?php if (mysqli_num_rows($result_stok_menipis) > 0): ?>
                                                            <ul class="list-group list-group-flush">
                                                                        <?php mysqli_data_seek($result_stok_menipis, 0); ?>
                                                                        <?php while ($produk = mysqli_fetch_assoc($result_stok_menipis)): ?>
                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                                <?= htmlspecialchars($produk['name']) ?>
                                                                                                <span class="badge bg-warning rounded-pill"><?= $produk['stock'] ?></span>
                                                                                    </li>
                                                                        <?php endwhile; ?>
                                                            </ul>
                                                <?php else: ?>
                                                            <p class="text-muted text-center mb-0">Tidak ada produk yang stoknya menipis.</p>
                                                <?php endif; ?>
                                    </div>
                        </div>
            </div>
</div>