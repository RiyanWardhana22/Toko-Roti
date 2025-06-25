<?php
if (!isset($_GET['id'])) {
            echo "<div class='alert alert-danger'>ID Pesanan tidak ditemukan.</div>";
            return;
}
$order_id = (int)$_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
            $new_status = mysqli_real_escape_string($conn, $_POST['status']);
            $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
            if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Status pesanan berhasil diperbarui.</div>";
            }
}

$order_res = mysqli_query($conn, "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = $order_id");
$order = mysqli_fetch_assoc($order_res);
$items_res = mysqli_query($conn, "SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
$confirmation_res = mysqli_query($conn, "SELECT * FROM payment_confirmations WHERE order_id = $order_id LIMIT 1");
$confirmation_data = mysqli_fetch_assoc($confirmation_res);

$statuses = ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail Pesanan #<?= $order['id'] ?></h1>
            <div>
                        <a href="<?= BASE_URL ?>admin?page=invoice&id=<?= $order['id'] ?>" target="_blank" class="btn btn-secondary">Cetak Faktur</a>
                        <a href="<?= BASE_URL ?>admin?page=orders" class="btn btn-primary">Kembali ke Daftar Pesanan</a>
            </div>
</div>

<?= $message ?>

<div class="row">
            <div class="col-md-4">
                        <h4>Detail Pelanggan</h4>
                        <p><strong>Nama:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?><br>
                                    <strong>Telepon:</strong> <?= htmlspecialchars($order['customer_phone']) ?>
                        </p>

                        <h4>Alamat Pengiriman</h4>
                        <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>

                        <?php if ($confirmation_data): ?>
                                    <div class="card mt-4">
                                                <div class="card-header bg-success text-white">
                                                            <strong>Detail Konfirmasi Pembayaran</strong>
                                                </div>
                                                <div class="card-body">
                                                            <p>
                                                                        <strong>Bank Pengirim:</strong> <?= htmlspecialchars($confirmation_data['bank_name']) ?><br>
                                                                        <strong>Pemilik Rekening:</strong> <?= htmlspecialchars($confirmation_data['account_holder']) ?><br>
                                                                        <strong>Jumlah Transfer:</strong> Rp <?= number_format($confirmation_data['transfer_amount'], 0, ',', '.') ?><br>
                                                                        <strong>Tanggal Transfer:</strong> <?= date('d M Y', strtotime($confirmation_data['transfer_date'])) ?>
                                                            </p>
                                                            <h6>Bukti Transfer:</h6>
                                                            <a href="<?= BASE_URL ?>assets/images/proofs/<?= htmlspecialchars($confirmation_data['proof_image_url']) ?>" target="_blank">
                                                                        <img src="<?= BASE_URL ?>assets/images/proofs/<?= htmlspecialchars($confirmation_data['proof_image_url']) ?>" class="img-fluid rounded" alt="Bukti Transfer">
                                                            </a>
                                                </div>
                                    </div>
                        <?php endif; ?>
            </div>
            <div class="col-md-8">
                        <h4>Detail Pesanan</h4>
                        <div class="row">
                                    <div class="col-md-6">
                                                <p><strong>Tanggal Pesan:</strong> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?><br>
                                                            <strong>Metode Pengiriman:</strong> <?= htmlspecialchars($order['shipping_method']) ?>
                                                </p>
                                    </div>
                                    <div class="col-md-6">
                                                <form method="POST" action="">
                                                            <div class="input-group">
                                                                        <label class="input-group-text" for="status">Status Pesanan</label>
                                                                        <select class="form-select" id="status" name="status">
                                                                                    <?php foreach ($statuses as $status): ?>
                                                                                                <option value="<?= $status ?>" <?= ($order['status'] == $status) ? 'selected' : '' ?>><?= $status ?></option>
                                                                                    <?php endforeach; ?>
                                                                        </select>
                                                                        <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                                            </div>
                                                </form>
                                    </div>
                        </div>
                        <h4 class="mt-4">Item yang Dipesan</h4>
                        <table class="table">
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
                                                            <th colspan="3" class="text-end">Subtotal Produk</th>
                                                            <th>Rp <?= number_format($order['total_amount'] - $order['shipping_cost'], 0, ',', '.') ?></th>
                                                </tr>
                                                <tr>
                                                            <th colspan="3" class="text-end">Ongkos Kirim</th>
                                                            <th>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></th>
                                                </tr>
                                                <tr>
                                                            <th colspan="3" class="text-end">Total Akhir</th>
                                                            <th>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></th>
                                                </tr>
                                    </tfoot>
                        </table>
            </div>
</div>