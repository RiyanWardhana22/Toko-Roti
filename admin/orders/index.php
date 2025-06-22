<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/navbar.php';

if (!isAdmin()) {
            redirect('/');
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$total_orders = countOrders($status, $date_from, $date_to);
$total_pages = ceil($total_orders / $per_page);

// Validate current page
if ($current_page < 1) {
            $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
}

$offset = ($current_page - 1) * $per_page;
$orders = getOrders($status, $date_from, $date_to, $per_page, $offset);
?>

<div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Manajemen Pesanan</h1>
            </div>

            <!-- Filter Card -->
            <div class="card mb-4">
                        <div class="card-body">
                                    <form method="get" class="row g-3">
                                                <div class="col-md-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="status">
                                                                        <option value="">Semua Status</option>
                                                                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                        <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Diproses</option>
                                                                        <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Dikirim</option>
                                                                        <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Selesai</option>
                                                                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                                                            </select>
                                                </div>
                                                <div class="col-md-3">
                                                            <label class="form-label">Dari Tanggal</label>
                                                            <input type="date" class="form-control" name="date_from" value="<?= $date_from ?>">
                                                </div>
                                                <div class="col-md-3">
                                                            <label class="form-label">Sampai Tanggal</label>
                                                            <input type="date" class="form-control" name="date_to" value="<?= $date_to ?>">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                                                            <a href="/admin/orders/" class="btn btn-outline-secondary">Reset</a>
                                                </div>
                                    </form>
                        </div>
            </div>

            <!-- Orders Table -->
            <div class="card shadow">
                        <div class="card-body">
                                    <div class="table-responsive">
                                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                                            <thead class="table-light">
                                                                        <tr>
                                                                                    <th width="50px">#</th>
                                                                                    <th>ID Pesanan</th>
                                                                                    <th>Pelanggan</th>
                                                                                    <th>Tanggal</th>
                                                                                    <th>Total</th>
                                                                                    <th>Status</th>
                                                                                    <th width="120px">Aksi</th>
                                                                        </tr>
                                                            </thead>
                                                            <tbody>
                                                                        <?php if (empty($orders)): ?>
                                                                                    <tr>
                                                                                                <td colspan="7" class="text-center py-4">Tidak ada pesanan ditemukan</td>
                                                                                    </tr>
                                                                        <?php else: ?>
                                                                                    <?php foreach ($orders as $index => $order): ?>
                                                                                                <tr>
                                                                                                            <td><?= $index + 1 + $offset ?></td>
                                                                                                            <td><?= $order['order_number'] ?></td>
                                                                                                            <td><?= $order['customer_name'] ?></td>
                                                                                                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                                                                            <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                                                            <td>
                                                                                                                        <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>">
                                                                                                                                    <?= getStatusText($order['status']) ?>
                                                                                                                        </span>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                        <div class="d-flex gap-2">
                                                                                                                                    <a href="/admin/orders/view.php?id=<?= $order['id'] ?>"
                                                                                                                                                class="btn btn-sm btn-info"
                                                                                                                                                title="Lihat">
                                                                                                                                                <i class="fas fa-eye"></i>
                                                                                                                                    </a>
                                                                                                                                    <a href="/admin/orders/edit.php?id=<?= $order['id'] ?>"
                                                                                                                                                class="btn btn-sm btn-warning"
                                                                                                                                                title="Edit">
                                                                                                                                                <i class="fas fa-edit"></i>
                                                                                                                                    </a>
                                                                                                                        </div>
                                                                                                            </td>
                                                                                                </tr>
                                                                                    <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                            </tbody>
                                                </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($total_pages > 1): ?>
                                                <nav aria-label="Page navigation" class="mt-4">
                                                            <ul class="pagination justify-content-center">
                                                                        <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                                                                                    <a class="page-link"
                                                                                                href="?page=<?= $current_page - 1 ?><?= $status ? '&status=' . $status : '' ?><?= $date_from ? '&date_from=' . $date_from : '' ?><?= $date_to ? '&date_to=' . $date_to : '' ?>"
                                                                                                aria-label="Previous">
                                                                                                <span aria-hidden="true">&laquo;</span>
                                                                                    </a>
                                                                        </li>

                                                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                                                                                <a class="page-link"
                                                                                                            href="?page=<?= $i ?><?= $status ? '&status=' . $status : '' ?><?= $date_from ? '&date_from=' . $date_from : '' ?><?= $date_to ? '&date_to=' . $date_to : '' ?>">
                                                                                                            <?= $i ?>
                                                                                                </a>
                                                                                    </li>
                                                                        <?php endfor; ?>

                                                                        <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                                                                                    <a class="page-link"
                                                                                                href="?page=<?= $current_page + 1 ?><?= $status ? '&status=' . $status : '' ?><?= $date_from ? '&date_from=' . $date_from : '' ?><?= $date_to ? '&date_to=' . $date_to : '' ?>"
                                                                                                aria-label="Next">
                                                                                                <span aria-hidden="true">&raquo;</span>
                                                                                    </a>
                                                                        </li>
                                                            </ul>
                                                </nav>
                                    <?php endif; ?>
                        </div>
            </div>
</div>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>