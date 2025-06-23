<?php
if (!isset($_GET['id'])) {
            echo "<div class='alert alert-danger'>ID Pelanggan tidak ditemukan.</div>";
            return;
}

$customer_id = (int)$_GET['id'];

$customer_stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? AND role = 'customer'");
mysqli_stmt_bind_param($customer_stmt, "i", $customer_id);
mysqli_stmt_execute($customer_stmt);
$customer_result = mysqli_stmt_get_result($customer_stmt);
$customer = mysqli_fetch_assoc($customer_result);

if (!$customer) {
            echo "<div class='alert alert-danger'>Data pelanggan tidak ditemukan.</div>";
            return;
}

$orders_stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($orders_stmt, "i", $customer_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail Pelanggan</h1>
            <a href="<?= BASE_URL ?>admin?page=customers" class="btn btn-secondary">Kembali ke Daftar Pelanggan</a>
</div>

<div class="row">
            <div class="col-md-4">
                        <div class="card">
                                    <div class="card-header">
                                                Profil Pelanggan
                                    </div>
                                    <div class="card-body">
                                                <p><strong>Nama:</strong><br><?= htmlspecialchars($customer['name']) ?></p>
                                                <p><strong>Email:</strong><br><?= htmlspecialchars($customer['email']) ?></p>
                                                <p><strong>Telepon:</strong><br><?= htmlspecialchars($customer['phone']) ?></p>
                                                <p><strong>Alamat:</strong><br><?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                                                <p><strong>Tanggal Bergabung:</strong><br><?= date('d F Y, H:i', strtotime($customer['created_at'])) ?></p>
                                    </div>
                        </div>
            </div>

            <div class="col-md-8">
                        [cite_start]<h3>Riwayat Belanja [cite: 76]</h3>
                        <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                                <thead>
                                                            <tr>
                                                                        <th>ID Pesanan</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Total</th>
                                                                        <th>Status</th>
                                                                        <th>Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                                                                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                                                                    <tr>
                                                                                                <td>#<?= $order['id'] ?></td>
                                                                                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                                                                <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                                                <td><span class="badge bg-primary"><?= htmlspecialchars($order['status']) ?></span></td>
                                                                                                <td>
                                                                                                            <a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Lihat Pesanan</a>
                                                                                                </td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else: ?>
                                                                        <tr>
                                                                                    <td colspan="5" class="text-center">Pelanggan ini belum pernah melakukan pemesanan.</td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>
</div>