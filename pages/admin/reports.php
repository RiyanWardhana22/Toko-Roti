<?php
$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

$sales_report_query = "
    SELECT 
        COUNT(id) as total_orders, 
        SUM(total_amount) as total_revenue
    FROM orders 
    WHERE 
        status = 'Selesai' AND 
        DATE(created_at) BETWEEN ? AND ?
";

$stmt_sales = mysqli_prepare($conn, $sales_report_query);
mysqli_stmt_bind_param($stmt_sales, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_sales);
$sales_report_result = mysqli_stmt_get_result($stmt_sales);
$sales_summary = mysqli_fetch_assoc($sales_report_result);

$detailed_orders_query = "
    SELECT o.id, u.name as customer_name, o.created_at, o.total_amount 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE 
        o.status = 'Selesai' AND 
        DATE(o.created_at) BETWEEN ? AND ?
    ORDER BY o.created_at DESC
";
$stmt_detailed = mysqli_prepare($conn, $detailed_orders_query);
mysqli_stmt_bind_param($stmt_detailed, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_detailed);
$detailed_orders_result = mysqli_stmt_get_result($stmt_detailed);

$bestselling_query = "
    SELECT 
        p.name as product_name,
        SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'Selesai'
    GROUP BY oi.product_id, p.name
    ORDER BY total_sold DESC
    LIMIT 10
";
$bestselling_result = mysqli_query($conn, $bestselling_query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Laporan Toko</h1>
</div>

<ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sales-report-tab" data-bs-toggle="tab" data-bs-target="#sales-report" type="button" role="tab">Laporan Penjualan</button>
            </li>
            <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bestselling-tab" data-bs-toggle="tab" data-bs-target="#bestselling" type="button" role="tab">Produk Terlaris</button>
            </li>
</ul>

<div class="tab-content" id="reportTabsContent">
            <div class="tab-pane fade show active" id="sales-report" role="tabpanel">
                        <div class="my-3 p-3 bg-light rounded">
                                    <h4>Filter Laporan Penjualan</h4>
                                    <form method="GET">
                                                <input type="hidden" name="page" value="reports">
                                                <div class="row">
                                                            <div class="col-md-5">
                                                                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                                                                        <input type="date" class="form-control" name="start_date" id="start_date" value="<?= $start_date ?>">
                                                            </div>
                                                            <div class="col-md-5">
                                                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                                                        <input type="date" class="form-control" name="end_date" id="end_date" value="<?= $end_date ?>">
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-end">
                                                                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                                                            </div>
                                                </div>
                                    </form>
                        </div>

                        <h5>Ringkasan untuk periode <?= date('d M Y', strtotime($start_date)) ?> s/d <?= date('d M Y', strtotime($end_date)) ?></h5>
                        <div class="row my-3">
                                    <div class="col-md-6">
                                                <div class="card text-bg-success">
                                                            <div class="card-body">
                                                                        <h5 class="card-title">Total Pendapatan</h5>
                                                                        <p class="card-text fs-4">Rp <?= number_format($sales_summary['total_revenue'] ?? 0, 0, ',', '.') ?></p>
                                                            </div>
                                                </div>
                                    </div>
                                    <div class="col-md-6">
                                                <div class="card text-bg-info">
                                                            <div class="card-body">
                                                                        <h5 class="card-title">Jumlah Pesanan Selesai</h5>
                                                                        <p class="card-text fs-4"><?= $sales_summary['total_orders'] ?? 0 ?> Pesanan</p>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <h6>Rincian Pesanan Selesai</h6>
                        <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                                <thead>
                                                            <tr>
                                                                        <th>ID Pesanan</th>
                                                                        <th>Nama Pelanggan</th>
                                                                        <th>Tanggal</th>
                                                                        <th>Total</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php if (mysqli_num_rows($detailed_orders_result) > 0): ?>
                                                                        <?php while ($order = mysqli_fetch_assoc($detailed_orders_result)): ?>
                                                                                    <tr>
                                                                                                <td>#<?= $order['id'] ?></td>
                                                                                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                                                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                                                                <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else: ?>
                                                                        <tr>
                                                                                    <td colspan="4" class="text-center">Tidak ada data penjualan untuk periode ini.</td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>

            <div class="tab-pane fade" id="bestselling" role="tabpanel">
                        <div class="my-3">
                                    <h4>10 Produk Terlaris</h4>
                                    <div class="table-responsive">
                                                <table class="table table-striped table-sm">
                                                            <thead>
                                                                        <tr>
                                                                                    <th>Peringkat</th>
                                                                                    <th>Nama Produk</th>
                                                                                    <th>Jumlah Terjual</th>
                                                                        </tr>
                                                            </thead>
                                                            <tbody>
                                                                        <?php if (mysqli_num_rows($bestselling_result) > 0): $rank = 1; ?>
                                                                                    <?php while ($product = mysqli_fetch_assoc($bestselling_result)): ?>
                                                                                                <tr>
                                                                                                            <td><?= $rank++ ?></td>
                                                                                                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                                                                            <td><?= $product['total_sold'] ?></td>
                                                                                                </tr>
                                                                                    <?php endwhile; ?>
                                                                        <?php else: ?>
                                                                                    <tr>
                                                                                                <td colspan="3" class="text-center">Belum ada produk yang terjual.</td>
                                                                                    </tr>
                                                                        <?php endif; ?>
                                                            </tbody>
                                                </table>
                                    </div>
                        </div>
            </div>
</div>