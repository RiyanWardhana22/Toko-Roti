<?php
if (!isset($_GET['id'])) {
            die("ID Pesanan tidak valid.");
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
                                    border-radius: 8px;
                                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .05);
                        }

                        .invoice-header {
                                    text-align: center;
                                    border-bottom: 2px solid #eee;
                                    padding-bottom: 20px;
                                    margin-bottom: 30px;
                        }

                        .invoice-header .brand-logo {
                                    max-height: 60px;
                                    margin-bottom: 15px;
                        }

                        .invoice-header .status {
                                    font-size: 1.5rem;
                                    font-weight: 700;
                                    color: #198754;
                        }

                        .invoice-header .total-amount {
                                    font-size: 2rem;
                                    font-weight: 700;
                                    color: #212529;
                        }

                        .invoice-header .order-date {
                                    color: #6c757d;
                        }

                        .info-section {
                                    display: flex;
                                    justify-content: space-between;
                                    margin-bottom: 30px;
                        }

                        .info-section div {
                                    width: 48%;
                        }

                        .info-section h5 {
                                    color: #6c757d;
                                    font-size: 1rem;
                                    margin-bottom: 10px;
                        }

                        .info-section address {
                                    font-style: normal;
                                    line-height: 1.6;
                        }

                        .items-table {
                                    width: 100%;
                                    border-collapse: collapse;
                        }

                        .items-table th,
                        .items-table td {
                                    padding: 12px 0;
                                    border-bottom: 1px solid #eee;
                        }

                        .items-table thead th {
                                    color: #6c757d;
                                    font-weight: 500;
                                    text-align: left;
                        }

                        .items-table .text-end {
                                    text-align: right;
                        }

                        .totals-section {
                                    margin-top: 20px;
                                    display: flex;
                                    justify-content: flex-end;
                        }

                        .totals-section table {
                                    width: 50%;
                                    max-width: 350px;
                        }

                        .totals-section td {
                                    padding: 8px 0;
                        }

                        .totals-section .grand-total {
                                    font-size: 1.2rem;
                                    font-weight: 700;
                                    border-top: 2px solid #333;
                                    padding-top: 10px;
                        }

                        .invoice-footer {
                                    text-align: center;
                                    margin-top: 40px;
                                    font-size: 0.9em;
                                    color: #6c757d;
                        }

                        .print-button {
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                        }

                        @media print {
                                    body {
                                                background-color: #fff;
                                    }

                                    .invoice-container {
                                                margin: 0;
                                                padding: 0;
                                                box-shadow: none;
                                                border: none;
                                    }

                                    .no-print {
                                                display: none;
                                    }
                        }
            </style>
</head>

<body>
            <div class="container no-print">
                        <div class="d-flex justify-content-end pt-3">
                                    <button class="btn btn-primary print-button" onclick="window.print()">
                                                <i class="fas fa-print me-2"></i>Cetak / Simpan PDF
                                    </button>
                        </div>
            </div>
            <div class="invoice-container">
                        <header class="invoice-header">
                                    <?php
                                    $brand_type = $site_settings['navbar_brand_type'] ?? 'text';
                                    if ($brand_type == 'logo' && !empty($site_settings['navbar_brand_logo'])) {
                                                echo '<img src="' . BASE_URL . 'assets/images/' . htmlspecialchars($site_settings['navbar_brand_logo']) . '" alt="Logo Toko" class="brand-logo">';
                                    } else {
                                                echo '<h3>' . htmlspecialchars($site_settings['navbar_brand_text'] ?? 'Toko Roti') . '</h3>';
                                    }
                                    ?>
                                    <div class="status">Pembayaran Berhasil</div>
                                    <div class="total-amount">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></div>
                                    <div class="order-date"><?= date('d F Y, H:i', strtotime($order['created_at'])) ?> WIB</div>
                        </header>

                        <section class="info-section">
                                    <div>
                                                <h5>Ditagihkan Kepada:</h5>
                                                <address>
                                                            <strong>Nama Pelanggan: </strong><?= htmlspecialchars($order['customer_name']) ?><br>
                                                            <strong>Nomor HP: </strong><?= htmlspecialchars($order['customer_phone']) ?><br>
                                                            <strong>Alamat: </strong><?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                                                </address>
                                    </div>
                                    <div>
                                                <h5>Detail Pesanan:</h5>
                                                <address>
                                                            <strong>No. Pesanan:</strong> <?= $order['id'] ?><br>
                                                            <strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['payment_method']) ?><br>
                                                            <strong>Metode Pengiriman:</strong> <?= htmlspecialchars($order['shipping_method']) ?>
                                                </address>
                                    </div>
                        </section>

                        <section>
                                    <table class="items-table">
                                                <thead>
                                                            <tr>
                                                                        <th>Deskripsi Produk</th>
                                                                        <th class="text-center">Jumlah</th>
                                                                        <th class="text-end">Total</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php mysqli_data_seek($items_res, 0);
                                                            while ($item = mysqli_fetch_assoc($items_res)): ?>
                                                                        <tr>
                                                                                    <td>
                                                                                                <?= htmlspecialchars($item['product_name']) ?>
                                                                                                <?php if (!empty($item['customization_details'])): ?>
                                                                                                            <br><small class="text-muted"><em>"<?= htmlspecialchars($item['customization_details']) ?>"</em></small>
                                                                                                <?php endif; ?>
                                                                                    </td>
                                                                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                                                                    <td class="text-end">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                                                        </tr>
                                                            <?php endwhile; ?>
                                                </tbody>
                                    </table>
                        </section>

                        <section class="totals-section">
                                    <table>
                                                <tbody>
                                                            <tr>
                                                                        <td class="text-muted">Subtotal</td>
                                                                        <td class="text-end">Rp <?= number_format($order['total_amount'] + $order['discount_amount'] - $order['shipping_cost'], 0, ',', '.') ?></td>
                                                            </tr>
                                                            <?php if ($order['discount_amount'] > 0): ?>
                                                                        <tr>
                                                                                    <td class="text-muted">Diskon (<?= htmlspecialchars($order['voucher_code']) ?>)</td>
                                                                                    <td class="text-end">- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                            <tr>
                                                                        <td class="text-muted">Ongkos Kirim</td>
                                                                        <td class="text-end">Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></td>
                                                            </tr>
                                                            <tr class="grand-total">
                                                                        <td>Total</td>
                                                                        <td class="text-end">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                            </tr>
                                                </tbody>
                                    </table>
                        </section>

                        <footer class="invoice-footer">
                                    <p>Terima kasih telah berbelanja di <?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti Anda') ?>!</p>
                        </footer>
            </div>
</body>

</html>