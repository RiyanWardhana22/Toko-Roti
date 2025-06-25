<?php
if (!isset($_GET['id'])) {
            die("ID Pesanan tidak valid atau tidak ditemukan.");
}
$order_id = (int)$_GET['id'];

$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$site_settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $site_settings[$row['setting_key']] = $row['setting_value'];
}

$order_res = mysqli_query($conn, "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = $order_id");
$order = mysqli_fetch_assoc($order_res);

if (!$order) {
            die("Pesanan dengan ID #$order_id tidak ditemukan.");
}

$items_res = mysqli_query($conn, "SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="id">

<head>
            <meta charset="UTF-8">
            <title>Invoice #<?= $order['id'] ?></title>
            <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
            <style>
                        body {
                                    background-color: #f8f9fa;
                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        }

                        .invoice-container {
                                    max-width: 800px;
                                    margin: 30px auto;
                                    padding: 40px;
                                    background-color: #ffffff;
                                    border: 1px solid #dee2e6;
                                    border-radius: 0.25rem;
                                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .15);
                        }

                        .invoice-header {
                                    border-bottom: 2px solid #dee2e6;
                                    margin-bottom: 30px;
                                    padding-bottom: 20px;
                        }

                        .invoice-footer {
                                    border-top: 2px solid #dee2e6;
                                    margin-top: 30px;
                                    padding-top: 20px;
                                    text-align: center;
                                    font-size: 0.9em;
                                    color: #6c757d;
                        }

                        .table th,
                        .table td {
                                    vertical-align: middle;
                        }

                        .total-section .table {
                                    max-width: 300px;
                                    float: right;
                        }

                        .brand-logo {
                                    max-height: 60px;
                        }

                        @media print {
                                    body {
                                                background-color: #ffffff;
                                    }

                                    .invoice-container {
                                                width: 100%;
                                                max-width: 100%;
                                                margin: 0;
                                                padding: 0;
                                                border: none;
                                                border-radius: 0;
                                                box-shadow: none;
                                    }

                                    .no-print {
                                                display: none;
                                    }
                        }
            </style>
</head>

<body>
            <div class="invoice-container">
                        <div class="invoice-header row align-items-center">
                                    <div class="col-sm-6">
                                                <?php
                                                $brand_type = $site_settings['navbar_brand_type'] ?? 'text';
                                                if ($brand_type == 'logo' && !empty($site_settings['navbar_brand_logo'])) {
                                                            echo '<img src="' . BASE_URL . 'assets/images/' . htmlspecialchars($site_settings['navbar_brand_logo']) . '" alt="Logo Toko" class="brand-logo">';
                                                } else {
                                                            $brand_text = $site_settings['navbar_brand_text'] ?? 'Toko Roti';
                                                            echo '<h2 class="mb-0">' . htmlspecialchars($brand_text) . '</h2>';
                                                }
                                                ?>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                                <h2 class="mb-0">INVOICE</h2>
                                                <p class="mb-0">No: #<?= $order['id'] ?></p>
                                                <button class="btn btn-primary mt-2 no-print" onclick="window.print()">
                                                            <i class="fas fa-print"></i> Cetak / Simpan PDF
                                                </button>
                                    </div>
                        </div>

                        <div class="row mb-4">
                                    <div class="col-sm-6">
                                                <strong>Ditagihkan Kepada:</strong>
                                                <address class="mt-2">
                                                            Nama: <?= htmlspecialchars($order['customer_name']) ?><br>
                                                            Alamat: <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                                                            Email: <?= htmlspecialchars($order['customer_email']) ?><br>
                                                            Telepon: <?= htmlspecialchars($order['customer_phone']) ?>
                                                </address>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                                <strong>Tanggal Pesanan:</strong>
                                                <p><?= date('d F Y', strtotime($order['created_at'])) ?></p>
                                                <strong>Status Pembayaran:</strong>
                                                <p><span class="badge bg-success">LUNAS</span></p>
                                    </div>
                        </div>

                        <div class="table-responsive">
                                    <table class="table table-bordered">
                                                <thead class="table-light">
                                                            <tr>
                                                                        <th class="text-center">#</th>
                                                                        <th>Produk</th>
                                                                        <th class="text-center">Jumlah</th>
                                                                        <th class="text-end">Harga Satuan</th>
                                                                        <th class="text-end">Subtotal</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php $no = 1;
                                                            while ($item = mysqli_fetch_assoc($items_res)): ?>
                                                                        <tr>
                                                                                    <td class="text-center"><?= $no++ ?></td>
                                                                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                                                                    <td class="text-end">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                                                    <td class="text-end">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                                                        </tr>
                                                            <?php endwhile; ?>
                                                </tbody>
                                    </table>
                        </div>

                        <div class="row">
                                    <div class="col-md-6">
                                                <p><strong>Metode Pengiriman:</strong> <?= htmlspecialchars($order['shipping_method']) ?></p>
                                    </div>
                                    <div class="col-md-6 total-section">
                                                <table class="table">
                                                            <tbody>
                                                                        <tr>
                                                                                    <td><strong>Subtotal Produk</strong></td>
                                                                                    <td class="text-end">Rp <?= number_format($order['total_amount'] - $order['shipping_cost'], 0, ',', '.') ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                                    <td><strong>Ongkos Kirim</strong></td>
                                                                                    <td class="text-end">Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></td>
                                                                        </tr>
                                                                        <tr class="fw-bold fs-5 table-light">
                                                                                    <td><strong>TOTAL</strong></td>
                                                                                    <td class="text-end"><strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></td>
                                                                        </tr>
                                                            </tbody>
                                                </table>
                                    </div>
                        </div>

                        <div class="invoice-footer">
                                    <p>Terima kasih telah berbelanja di toko kami. Jika ada pertanyaan mengenai invoice ini, silakan hubungi kami.</p>
                                    <p><?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti Lezat') ?></p>
                        </div>
            </div>
</body>

</html>