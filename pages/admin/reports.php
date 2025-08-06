<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    echo '<div class="alert alert-danger text-center">Anda tidak memiliki hak akses untuk melihat halaman ini.</div>';
    return;
}

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

$bestselling_result = mysqli_query($conn, "SELECT p.name as product_name, SUM(oi.quantity) as total_sold FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE o.status = 'Selesai' GROUP BY oi.product_id, p.name ORDER BY total_sold DESC LIMIT 10");

$chart_data_query = "SELECT DATE(created_at) as sale_date, SUM(total_amount) as daily_revenue FROM orders WHERE status = 'Selesai' AND DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY sale_date ASC";
$stmt_chart = mysqli_prepare($conn, $chart_data_query);
mysqli_stmt_bind_param($stmt_chart, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt_chart);
$chart_result = mysqli_stmt_get_result($stmt_chart);

$chart_labels = [];
$chart_values = [];
while ($row = mysqli_fetch_assoc($chart_result)) {
    $chart_labels[] = date('d M', strtotime($row['sale_date']));
    $chart_values[] = $row['daily_revenue'];
}
?>

<div class="card content-card">
    <div class="card-header">Filter Laporan</div>
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
    <div class="col-lg-6 mb-4">
        <div class="card info-card card-green h-100">
            <div class="card-body">
                <div class="info-card-text">
                    <p class="mb-1">Total Pendapatan</p>
                    <h4 class="mb-0">Rp <?= number_format($sales_summary['total_revenue'] ?? 0) ?></h4>
                </div>
                <div class="info-card-icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card info-card card-blue h-100">
            <div class="card-body">
                <div class="info-card-text">
                    <p class="mb-1">Pesanan Selesai</p>
                    <h4 class="mb-0"><?= $sales_summary['total_orders'] ?? 0 ?></h4>
                </div>
                <div class="info-card-icon"><i class="fas fa-box-open"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card content-card">
    <div class="card-header">
        Grafik Tren Penjualan (<?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>)
    </div>
    <div class="card-body">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<div class="card content-card">
    <div class="card-header">10 Produk Terlaris</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');

        const chartLabels = <?= json_encode($chart_labels) ?>;
        const chartData = <?= json_encode($chart_values) ?>;

        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: chartData,
                    backgroundColor: 'rgba(74, 105, 255, 0.1)',
                    borderColor: 'rgba(74, 105, 255, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>