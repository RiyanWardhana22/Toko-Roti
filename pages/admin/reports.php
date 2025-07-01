<?php
$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

$sales_report_query = "SELECT COUNT(id) as total_orders, SUM(total_amount) as total_revenue FROM orders WHERE status = 'Selesai' AND DATE(created_at) BETWEEN ? AND ?";
$stmt_sales = mysqli_prepare($conn, $sales_report_query);
mysqli_stmt_bind_param($stmt_sales, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_sales);
$sales_summary = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_sales));

$detailed_orders_query = "SELECT o.id, u.name as customer_name, o.created_at, o.total_amount FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = 'Selesai' AND DATE(o.created_at) BETWEEN ? AND ? ORDER BY o.created_at DESC";
$stmt_detailed = mysqli_prepare($conn, $detailed_orders_query);
mysqli_stmt_bind_param($stmt_detailed, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_detailed);
$detailed_orders_result = mysqli_stmt_get_result($stmt_detailed);

$bestselling_query = "SELECT p.name as product_name, SUM(oi.quantity) as total_sold FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE o.status = 'Selesai' GROUP BY oi.product_id, p.name ORDER BY total_sold DESC LIMIT 10";
$bestselling_result = mysqli_query($conn, $bestselling_query);
?>

<div class="card content-card">
    <div class="card-header">
        Filter Laporan Penjualan
    </div>
    <div class="card-body">
        <form method="GET">
            <input type="hidden" name="page" value="reports">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" value="<?= $start_date ?>">
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" value="<?= $end_date ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-header">
                Laporan Penjualan (<?= date('d-m-Y', strtotime($start_date)) ?> - <?= date('d-m-Y', strtotime($end_date)) ?>)
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-title">Total Pendapatan</div>
                            <div class="stat-value">Rp <?= number_format($sales_summary['total_revenue'] ?? 0) ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-title">Jumlah Pesanan Selesai</div>
                            <div class="stat-value"><?= $sales_summary['total_orders'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <h6>Rincian Pesanan Selesai</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($detailed_orders_result) > 0): while ($order = mysqli_fetch_assoc($detailed_orders_result)): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                        <td>Rp <?= number_format($order['total_amount']) ?></td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="text-center p-3">Tidak ada data.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card content-card">
            <div class="card-header">10 Produk Terlaris</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Produk</th>
                            <th>Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($bestselling_result) > 0): $rank = 1; ?>
                            <?php while ($product = mysqli_fetch_assoc($bestselling_result)): ?>
                                <tr>
                                    <td><span class="badge rounded-pill text-bg-primary"><?= $rank++ ?></span></td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td><strong><?= $product['total_sold'] ?></strong></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center p-3">Belum ada produk yang terjual.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>