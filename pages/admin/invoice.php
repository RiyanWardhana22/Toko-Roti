<?php
if (!isset($_GET['id'])) die("ID Pesanan tidak valid.");
$order_id = (int)$_GET['id'];

$order_res = mysqli_query($conn, "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = $order_id");
$order = mysqli_fetch_assoc($order_res);

$items_res = mysqli_query($conn, "SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="id">

<head>
            <meta charset="UTF-8">
            <title>Faktur #<?= $order_id ?></title>
            <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
            <style>
                        body {
                                    background-color: white;
                        }

                        @media print {
                                    .no-print {
                                                display: none;
                                    }
                        }
            </style>
</head>

<body class="p-4">
            <div class="container">
                        <div class="d-flex justify-content-between align-items-center">
                                    <h1>Faktur</h1>
                                    <button class="btn btn-primary no-print" onclick="window.print()">Cetak</button>
                        </div>
                        <p><strong>Nomor Pesanan:</strong> #<?= $order['id'] ?><br>
                                    <strong>Tanggal:</strong> <?= date('d F Y', strtotime($order['created_at'])) ?>
                        </p>
                        <hr>
                        <div class="row">
                                    <div class="col-6">
                                                <h4>Ditagihkan Kepada:</h4>
                                                <p>
                                                            <?= htmlspecialchars($order['customer_name']) ?><br>
                                                            <?= htmlspecialchars($order['customer_email']) ?><br>
                                                            <?= htmlspecialchars($order['customer_phone']) ?><br>
                                                            <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                                                </p>
                                    </div>
                                    <div class="col-6 text-end">
                                                <h4>Toko Roti Lezat</h4>
                                                <p>
                                                            Jl. Roti Enak No. 123<br>
                                                            Medan, Indonesia<br>
                                                            Email: kontak@tokoroti.com
                                                </p>
                                    </div>
                        </div>
                        <h4 class="mt-4">Rincian Pesanan:</h4>
                        <table class="table table-bordered">
                                    <thead>
                                                <tr>
                                                            <th>Produk</th>
                                                            <th>Jumlah</th>
                                                            <th>Harga Satuan</th>
                                                            <th>Subtotal</th>
                                                </tr>
                                    </thead>
                                    <tbody>
                                                <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                                                            <tr>
                                                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                                        <td><?= $item['quantity'] ?></td>
                                                                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                                        <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                                            </tr>
                                                <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                                <tr>
                                                            <td colspan="2"></td>
                                                            <td class="text-end"><strong>Subtotal</strong></td>
                                                            <td>Rp <?= number_format($order['total_amount'] - $order['shipping_cost'], 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                            <td colspan="2"></td>
                                                            <td class="text-end"><strong>Ongkos Kirim</strong></td>
                                                            <td>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                            <td colspan="2"></td>
                                                            <td class="text-end"><strong>Total Akhir</strong></td>
                                                            <td><strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></td>
                                                </tr>
                                    </tfoot>
                        </table>
                        <p class="text-center mt-4">Terima kasih telah berbelanja di Toko Roti Lezat!</p>
            </div>
</body>

</html>