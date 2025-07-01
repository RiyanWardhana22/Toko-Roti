<?php
if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            include 'order_detail.php';
            return;
}

/**
 * @param string 
 * @return string 
 */
function get_status_badge(string $status): string
{
            $class = '';
            switch ($status) {
                        case 'Menunggu Pembayaran':
                                    $class = 'text-bg-warning';
                                    break;
                        case 'Menunggu Verifikasi':
                        case 'Diproses':
                        case 'Dikirim':
                                    $class = 'text-bg-primary';
                                    break;
                        case 'Selesai':
                                    $class = 'text-bg-success';
                                    break;
                        case 'Dibatalkan':
                                    $class = 'text-bg-danger';
                                    break;
                        default:
                                    $class = 'text-bg-secondary';
                                    break;
            }
            return '<span class="badge fs-7 rounded-pill ' . $class . '">' . htmlspecialchars($status) . '</span>';
}

$status_filter = $_GET['status'] ?? '';
$statuses = ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];

$sql = "SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id";

if (!empty($status_filter) && in_array($status_filter, $statuses)) {
            $sql .= " WHERE o.status = ?";
}

$sql .= " ORDER BY o.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($status_filter) && in_array($status_filter, $statuses)) {
            mysqli_stmt_bind_param($stmt, 's', $status_filter);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="card content-card">
            <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center">
                                    <span class="me-3">Filter Status:</span>
                                    <div class="btn-group">
                                                <a href="<?= BASE_URL ?>admin?page=orders" class="btn btn-sm <?= empty($status_filter) ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
                                                <?php foreach ($statuses as $status) : ?>
                                                            <a href="<?= BASE_URL ?>admin?page=orders&status=<?= urlencode($status) ?>" class="btn btn-sm <?= ($status_filter == $status) ? 'btn-primary' : 'btn-outline-primary' ?>"><?= $status ?></a>
                                                <?php endforeach; ?>
                                    </div>
                        </div>
            </div>
</div>

<div class="card content-card">
            <div class="card-header">
                        Daftar Pesanan
            </div>
            <div class="card-body">
                        <div class="table-responsive">
                                    <table class="table table-hover">
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
                                                                                                <td><a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $order['id'] ?>" class="fw-bold">#<?= $order['id'] ?></a></td>
                                                                                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                                                                <td><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                                                                                                <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                                                <td><?= get_status_badge($order['status']) ?></td>
                                                                                                <td>
                                                                                                            <a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $order['id'] ?>" class="btn btn-primary btn-sm">Lihat Detail</a>
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
            </div>
</div>