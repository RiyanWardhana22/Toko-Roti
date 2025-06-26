<?php
$user_id = $_SESSION['user_id'];

$limit = 8;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;

$total_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE user_id = $user_id");
$total_results = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_results / $limit);
$orders_res = mysqli_query($conn, "SELECT id, created_at, total_amount, status FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
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
                                                                                                <a href="<?= BASE_URL ?>payment_confirmation?id=<?= $order['id'] ?>" class="btn btn-success btn-sm">Konfirmasi</a>
                                                                                    <?php endif; ?>
                                                                                    <?php if ($order['status'] == 'Dikirim'): ?>
                                                                                                <a href="<?= BASE_URL ?>app/order_customer_action.php?action=complete&id=<?= $order['id'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('Apakah Anda yakin sudah menerima pesanan ini?')">Pesanan Diterima</a>
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

<?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                                <a class="page-link" href="<?= BASE_URL ?>akun?tab=riwayat_pesanan&p=<?= $page - 1 ?>">Previous</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                            <a class="page-link" href="<?= BASE_URL ?>akun?tab=riwayat_pesanan&p=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                                <a class="page-link" href="<?= BASE_URL ?>akun?tab=riwayat_pesanan&p=<?= $page + 1 ?>">Next</a>
                                    </li>
                        </ul>
            </nav>
<?php endif; ?>