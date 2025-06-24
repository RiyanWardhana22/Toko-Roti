<?php
$user_id = $_SESSION['user_id'];
$orders_res = mysqli_query($conn, "SELECT id, created_at, total_amount, status FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<h3 class="card-title">Riwayat Pesanan</h3>

<div class="table-responsive">
            <table class="table table-bordered table-striped">
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
                                    <?php if (mysqli_num_rows($orders_res) > 0): ?>
                                                <?php while ($order = mysqli_fetch_assoc($orders_res)): ?>
                                                            <tr>
                                                                        <td>#<?= $order['id'] ?></td>
                                                                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                                        <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                        <td><span class="badge bg-primary"><?= htmlspecialchars($order['status']) ?></span></td>
                                                                        <td>
                                                                                    <a href="<?= BASE_URL ?>order_detail_customer?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Detail</a>

                                                                                    <?php if ($order['status'] == 'Menunggu Pembayaran'): ?>
                                                                                                <a href="<?= BASE_URL ?>payment_confirmation?id=<?= $order['id'] ?>" class="btn btn-success btn-sm">Konfirmasi Pembayaran</a>
                                                                                    <?php endif; ?>
                                                                        </td>
                                                            </tr>
                                                <?php endwhile; ?>
                                    <?php else: ?>
                                                <tr>
                                                            <td colspan="5" class="text-center">Anda belum memiliki riwayat pesanan.</td>
                                                </tr>
                                    <?php endif; ?>
                        </tbody>
            </table>
</div>