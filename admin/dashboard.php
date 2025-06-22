<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/navbar.php';

if (!isAdmin()) {
            redirect('/');
}

// Get dashboard statistics
$stats = getDashboardStatistics();
$recent_orders = getRecentOrders(5);
$low_stock_products = getLowStockProducts(5);
?>

<!-- Dashboard Content -->
<div class="container-fluid py-4">
            <h1 class="h3 mb-4">Dashboard</h1>

            <!-- Stats Cards -->
            <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                                <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                        <div class="col mr-2">
                                                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                                                                Pendapatan Hari Ini</div>
                                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                                                Rp <?= number_format($stats['today_income'], 0, ',', '.') ?>
                                                                                    </div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                                <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                        <div class="col mr-2">
                                                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                                                                Pesanan Hari Ini</div>
                                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                                                <?= $stats['today_orders'] ?>
                                                                                    </div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                                <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                        <div class="col mr-2">
                                                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                                                                Pelanggan Terdaftar</div>
                                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                                                <?= $stats['total_customers'] ?>
                                                                                    </div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                                <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                        <div class="col mr-2">
                                                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                                                                Produk</div>
                                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                                                <?= $stats['total_products'] ?>
                                                                                    </div>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                                    <i class="fas fa-box-open fa-2x text-gray-300"></i>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                        <!-- Sales Chart -->
                        <div class="col-xl-8">
                                    <div class="card shadow mb-4">
                                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                            <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan 7 Hari Terakhir</h6>
                                                </div>
                                                <div class="card-body">
                                                            <div class="chart-area">
                                                                        <canvas id="salesChart"></canvas>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <!-- Status Orders -->
                        <div class="col-xl-4">
                                    <div class="card shadow mb-4">
                                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                            <h6 class="m-0 font-weight-bold text-primary">Status Pesanan</h6>
                                                </div>
                                                <div class="card-body">
                                                            <div class="chart-pie pt-4 pb-2">
                                                                        <canvas id="orderStatusChart"></canvas>
                                                            </div>
                                                            <div class="mt-4 text-center small">
                                                                        <span class="mr-2">
                                                                                    <i class="fas fa-circle text-primary"></i> Pending
                                                                        </span>
                                                                        <span class="mr-2">
                                                                                    <i class="fas fa-circle text-success"></i> Selesai
                                                                        </span>
                                                                        <span class="mr-2">
                                                                                    <i class="fas fa-circle text-info"></i> Diproses
                                                                        </span>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>

            <!-- Recent Orders -->
            <div class="row mb-4">
                        <div class="col-12">
                                    <div class="card shadow mb-4">
                                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                            <h6 class="m-0 font-weight-bold text-primary">Pesanan Terbaru</h6>
                                                            <a href="/admin/orders/" class="btn btn-sm btn-primary">Lihat Semua</a>
                                                </div>
                                                <div class="card-body">
                                                            <div class="table-responsive">
                                                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                                                                    <thead>
                                                                                                <tr>
                                                                                                            <th>ID Pesanan</th>
                                                                                                            <th>Pelanggan</th>
                                                                                                            <th>Tanggal</th>
                                                                                                            <th>Total</th>
                                                                                                            <th>Status</th>
                                                                                                            <th>Aksi</th>
                                                                                                </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                                <?php foreach ($recent_orders as $order): ?>
                                                                                                            <tr>
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
                                                                                                                                    <a href="/admin/orders/view.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">
                                                                                                                                                <i class="fas fa-eye"></i>
                                                                                                                                    </a>
                                                                                                                        </td>
                                                                                                            </tr>
                                                                                                <?php endforeach; ?>
                                                                                    </tbody>
                                                                        </table>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>

            <!-- Low Stock Products -->
            <div class="row">
                        <div class="col-12">
                                    <div class="card shadow mb-4">
                                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                            <h6 class="m-0 font-weight-bold text-danger">Produk dengan Stok Menipis</h6>
                                                            <a href="/admin/products/" class="btn btn-sm btn-danger">Lihat Semua</a>
                                                </div>
                                                <div class="card-body">
                                                            <div class="table-responsive">
                                                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                                                                    <thead>
                                                                                                <tr>
                                                                                                            <th>Produk</th>
                                                                                                            <th>Kategori</th>
                                                                                                            <th>Stok</th>
                                                                                                            <th>Harga</th>
                                                                                                            <th>Aksi</th>
                                                                                                </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                                <?php foreach ($low_stock_products as $product): ?>
                                                                                                            <tr>
                                                                                                                        <td><?= $product['name'] ?></td>
                                                                                                                        <td><?= $product['category_name'] ?></td>
                                                                                                                        <td>
                                                                                                                                    <span class="badge bg-<?= $product['stock'] <= 5 ? 'danger' : 'warning' ?>">
                                                                                                                                                <?= $product['stock'] ?>
                                                                                                                                    </span>
                                                                                                                        </td>
                                                                                                                        <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                                                                                                        <td>
                                                                                                                                    <a href="/admin/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">
                                                                                                                                                <i class="fas fa-edit"></i>
                                                                                                                                    </a>
                                                                                                                        </td>
                                                                                                            </tr>
                                                                                                <?php endforeach; ?>
                                                                                    </tbody>
                                                                        </table>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                        type: 'line',
                        data: {
                                    labels: <?= json_encode(array_keys($stats['last_7_days_sales'])) ?>,
                                    datasets: [{
                                                label: 'Pendapatan',
                                                data: <?= json_encode(array_values($stats['last_7_days_sales'])) ?>,
                                                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                                borderColor: 'rgba(78, 115, 223, 1)',
                                                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                                                pointBorderColor: '#fff',
                                                pointHoverBackgroundColor: '#fff',
                                                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                                                pointRadius: 3,
                                                pointHoverRadius: 5,
                                                borderWidth: 2,
                                                fill: true
                                    }]
                        },
                        options: {
                                    maintainAspectRatio: false,
                                    scales: {
                                                y: {
                                                            beginAtZero: true,
                                                            ticks: {
                                                                        callback: function(value) {
                                                                                    return 'Rp ' + value.toLocaleString();
                                                                        }
                                                            }
                                                }
                                    },
                                    plugins: {
                                                tooltip: {
                                                            callbacks: {
                                                                        label: function(context) {
                                                                                    return 'Rp ' + context.raw.toLocaleString();
                                                                        }
                                                            }
                                                }
                                    }
                        }
            });

            // Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                                    labels: ['Pending', 'Selesai', 'Diproses'],
                                    datasets: [{
                                                data: [
                                                            <?= $stats['order_status_counts']['pending'] ?>,
                                                            <?= $stats['order_status_counts']['completed'] ?>,
                                                            <?= $stats['order_status_counts']['processing'] ?>
                                                ],
                                                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                                                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                                    }],
                        },
                        options: {
                                    maintainAspectRatio: false,
                                    plugins: {
                                                legend: {
                                                            display: false
                                                }
                                    },
                                    cutout: '70%',
                        },
            });
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>