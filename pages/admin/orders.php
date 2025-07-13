<?php
if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            include 'order_detail.php';
            return;
}

function get_status_badge(string $status): string
{
            $class = '';
            switch ($status) {
                        case 'Menunggu Pembayaran':
                                    $class = 'text-bg-warning';
                                    break;
                        case 'Menunggu Verifikasi':
                                    $class = 'text-bg-info';
                                    break;
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

$limit = 20;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;

$status_filter = $_GET['status'] ?? '';
$statuses = ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
$where_clause = '';
$params = [];
$types = '';

if (!empty($status_filter) && in_array($status_filter, $statuses)) {
            $where_clause = " WHERE o.status = ?";
            $params[] = $status_filter;
            $types .= 's';
}

$total_sql = "SELECT COUNT(o.id) as total FROM orders o" . $where_clause;
$total_stmt = mysqli_prepare($conn, $total_sql);
if (!empty($status_filter)) {
            mysqli_stmt_bind_param($total_stmt, 's', $status_filter);
}
mysqli_stmt_execute($total_stmt);
$total_results = mysqli_fetch_assoc(mysqli_stmt_get_result($total_stmt))['total'];
$total_pages = ceil($total_results / $limit);

$sql = "SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id" . $where_clause . " ORDER BY o.created_at DESC LIMIT ?, ?";
$stmt = mysqli_prepare($conn, $sql);

$params[] = $offset;
$params[] = $limit;
$types .= 'ii';

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="card content-card">
            <div class="card-body">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
                                    <span class="mb-2 mb-sm-0 me-sm-3 flex-shrink-0">Filter Status:</span>
                                    <select class="form-select form-select-sm" style="width: auto;" onchange="if (this.value) window.location = this.value;">
                                                <option value="<?= BASE_URL ?>admin?page=orders" <?= empty($status_filter) ? 'selected' : '' ?>>Semua</option>
                                                <?php foreach ($statuses as $status) : ?>
                                                            <option value="<?= BASE_URL ?>admin?page=orders&status=<?= urlencode($status) ?>" <?= ($status_filter == $status) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($status) ?>
                                                            </option>
                                                <?php endforeach; ?>
                                    </select>
                        </div>
            </div>
            <div class="card-header">Daftar Pesanan</div>
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
                                                                        <th class="text-center">Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php if (mysqli_num_rows($result) > 0) : ?>
                                                                        <?php while ($order = mysqli_fetch_assoc($result)) : ?>
                                                                                    <tr>
                                                                                                <td><a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $order['id'] ?>" class="fw-bold">#<?= $order['id'] ?></a></td>
                                                                                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                                                                <td><?= date('d-m-Y | H:i', strtotime($order['created_at'])) ?></td>
                                                                                                <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                                                <td><?= get_status_badge($order['status']) ?></td>
                                                                                                <td class="text-center">
                                                                                                            <a href="<?= BASE_URL ?>admin?page=orders&action=view&id=<?= $order['id'] ?>" class="btn btn-outline-info btn-sm"><i class="fa-solid fa-magnifying-glass-plus"></i></a>
                                                                                                </td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else : ?>
                                                                        <tr>
                                                                                    <td colspan="6" class="text-center p-4">Tidak ada pesanan yang cocok dengan filter ini.</td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                </tbody>
                                    </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                                    <nav class="mt-3">
                                                <ul class="pagination justify-content-center">
                                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                                                    <a class="page-link" href="?page=orders&status=<?= urlencode($status_filter) ?>&p=<?= $i ?>"><?= $i ?></a>
                                                                        </li>
                                                            <?php endfor; ?>
                                                </ul>
                                    </nav>
                        <?php endif; ?>
            </div>
</div>