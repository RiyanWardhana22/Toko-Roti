<?php
if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            include 'order_detail.php';
            return;
}
if (isset($_GET['action']) && $_GET['action'] == 'invoice' && isset($_GET['id'])) {
            include 'invoice.php';
            return;
}

$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where_clause = '';
if (!empty($status_filter)) {
            $where_clause = "WHERE o.status = '$status_filter'";
}

$result = mysqli_query($conn, "SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id $where_clause ORDER BY o.created_at DESC");

$statuses = ['Menunggu Pembayaran', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manajemen Pesanan</h1>
</div>

<div class="d-flex justify-content-start align-items-center mb-3">
            <span class="me-2">Filter Status:</span>
            <div class="btn-group">
                        <a href="<?= BASE_URL ?>admin?page=orders" class="btn btn-sm btn-outline-secondary <?= empty($status_filter) ? 'active' : '' ?>">Semua</a>
                        <?php foreach ($statuses as $status) : ?>
                                    <a href="<?= BASE_URL ?>admin?page=orders&status=<?= urlencode($status) ?>" class="btn btn-sm btn-outline-secondary <?= ($status_filter == $status) ? 'active' : '' ?>"><?= $status ?></a>
                        <?php endforeach; ?>
            </div>
</div>

<div class="table-responsive">
            <table class="table table-striped table-sm">
                        <thead>
                                    <tr>
                                                <th>ID Pesanan</th>
                                                <th>Nama Pelanggan</th>
                                                <th>Tanggal</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                    </tr>
                        </thead>
                        <tbody>
                                    <?php if (mysqli_num_rows($result) > 0) : ?>
                                                <?php while ($order = mysqli_fetch_assoc($result)) : ?>
                                                            <tr>
                                                                        <td>#<?= $order['id'] ?></td>
                                                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                                        <td><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                                                                        <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                        <td><span class="badge text-bg-info"><?= htmlspecialchars($order['status']) ?></span></td>
                                                                        <td>
                                                                                    <a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Lihat Detail</a>
                                                                        </td>
                                                            </tr>
                                                <?php endwhile; ?>
                                    <?php else : ?>
                                                <tr>
                                                            <td colspan="6" class="text-center">Tidak ada pesanan yang cocok dengan filter ini.</td>
                                                </tr>
                                    <?php endif; ?>
                        </tbody>
            </table>
</div>