<?php
if (!isset($_GET['id'])) {
            echo "<div class='alert alert-danger'>ID Pesanan tidak ditemukan.</div>";
            return;
}
$order_id = (int)$_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
            $new_status = mysqli_real_escape_string($conn, $_POST['status']);
            if ($new_status == 'Dikirim') {
                        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ?, shipped_at = NOW() WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
            } else {
                        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
            }

            if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Status pesanan berhasil diperbarui.</div>";
                        require_once __DIR__ . '/../../app/mailer.php';
                        $cust_res = mysqli_query($conn, "SELECT u.name, u.email FROM users u JOIN orders o ON u.id = o.user_id WHERE o.id = $order_id");
                        $customer_data = mysqli_fetch_assoc($cust_res);
                        $settings_res = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'website_title'");
                        $website_name = mysqli_fetch_assoc($settings_res)['setting_value'] ?? 'Toko Anda';

                        $template_path = __DIR__ . '/../../templates/email/status_update_template.html';
                        if (file_exists($template_path)) {
                                    $email_body = file_get_contents($template_path);
                                    $placeholders = [
                                                '{{customer_name}}' => $customer_data['name'],
                                                '{{order_id}}' => $order_id,
                                                '{{new_status}}' => $new_status,
                                                '{{order_detail_link}}' => BASE_URL . 'order_detail_customer?id=' . $order_id,
                                                '{{website_name}}' => $website_name,
                                                '{{current_year}}' => date('Y')
                                    ];
                                    $email_body = str_replace(array_keys($placeholders), array_values($placeholders), $email_body);
                                    send_email($customer_data['email'], $customer_data['name'], "Update Status Pesanan #" . $order_id, $email_body);
                        }
            }
}

$order_res = mysqli_query($conn, "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = $order_id");
$order = mysqli_fetch_assoc($order_res);

$items_res = mysqli_query($conn, "SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");

$confirmation_res = mysqli_query($conn, "SELECT * FROM payment_confirmations WHERE order_id = $order_id LIMIT 1");
$confirmation_data = mysqli_fetch_assoc($confirmation_res);

$statuses = ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
?>

<?= $message ?>

<div class="row">
            <div class="col-lg-4">
                        <div class="card content-card mb-4">
                                    <div class="card-header">Detail Pelanggan</div>
                                    <div class="card-body">
                                                <p><strong>Nama:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
                                                            <strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?><br>
                                                            <strong>Telepon:</strong> <?= htmlspecialchars($order['customer_phone']) ?>
                                                </p>
                                                <hr>
                                                <h6>Alamat Pengiriman</h6>
                                                <address><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></address>
                                    </div>
                        </div>

                        <?php if ($confirmation_data): ?>
                                    <div class="card content-card">
                                                <div class="card-header">Detail Konfirmasi Pembayaran</div>
                                                <div class="card-body">
                                                            <p>
                                                                        <strong>Bank Pengirim:</strong> <?= htmlspecialchars($confirmation_data['bank_name']) ?><br>
                                                                        <strong>Pemilik Rekening:</strong> <?= htmlspecialchars($confirmation_data['account_holder']) ?><br>
                                                                        <strong>Jumlah Transfer:</strong> Rp <?= number_format($confirmation_data['transfer_amount'], 0, ',', '.') ?><br>
                                                                        <strong>Tanggal Transfer:</strong> <?php
                                                                                                            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                                                                                            $timestamp = strtotime($confirmation_data['transfer_date']);
                                                                                                            echo $hari[date('w', $timestamp)] . ', ' . date('d/m/Y', $timestamp);
                                                                                                            ?>
                                                            </p>
                                                            <h6>Bukti Transfer:</h6>
                                                            <a href="<?= BASE_URL ?>assets/images/proofs/<?= htmlspecialchars($confirmation_data['proof_image_url']) ?>" target="_blank">
                                                                        <img src="<?= BASE_URL ?>assets/images/proofs/<?= htmlspecialchars($confirmation_data['proof_image_url']) ?>" class="img-fluid rounded border" alt="Bukti Transfer">
                                                            </a>
                                                </div>
                                    </div>
                        <?php endif; ?>
            </div>

            <div class="col-lg-8">
                        <div class="card content-card">
                                    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                                <h5 class="mb-2 mb-md-0">Rincian Pesanan #<?= $order['id'] ?></h5>
                                                <form method="POST" action="">
                                                            <div class="input-group">
                                                                        <select class="form-select" id="status" name="status" style="width: 150px;">
                                                                                    <?php foreach ($statuses as $status): ?>
                                                                                                <option value="<?= $status ?>" <?= ($order['status'] == $status) ? 'selected' : '' ?>><?= $status ?></option>
                                                                                    <?php endforeach; ?>
                                                                        </select>
                                                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                                            </div>
                                                </form>
                                                <div class="d-flex justify-content-start mt-2">
                                                            <a href="<?= BASE_URL ?>admin?page=invoice&id=<?= $order_id ?>" target="_blank" class="btn btn-outline-success btn-sm fw-bold"><i class="fa-solid fa-file-pdf me-2"></i>Cetak</a>
                                                </div>
                                    </div>

                                    <div class="card-body">
                                                <div class="table-responsive">
                                                            <table class="table">
                                                                        <thead>
                                                                                    <tr>
                                                                                                <th>Produk</th>
                                                                                                <th class="text-center">Jumlah</th>
                                                                                                <th class="text-end">Harga Satuan</th>
                                                                                                <th class="text-end">Subtotal</th>
                                                                                    </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                                                                                                <tr>
                                                                                                            <td>
                                                                                                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>

                                                                                                            </td>
                                                                                                            <?php if (!empty($item['customization_details'])): ?>
                                                                                                                        <p class="mb-1"><small class="text-muted"><em>Catatan: "<?= htmlspecialchars($item['customization_details']) ?>"</em></small></p>
                                                                                                            <?php endif; ?>
                                                                                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                                                                                            <td class="text-end">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                                                                            <td class="text-end">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>

                                                                                                </tr>

                                                                                    <?php endwhile; ?>
                                                                        </tbody>

                                                                        <tfoot>

                                                                                    <tr>
                                                                                                <th colspan="3" class="text-end">Subtotal Produk</th>
                                                                                                <th class="text-end">Rp <?= number_format($order['total_amount'] + $order['discount_amount'] - $order['shipping_cost'], 0, ',', '.') ?></th>
                                                                                    </tr>
                                                                                    <?php if ($order['discount_amount'] > 0): ?>
                                                                                                <tr class="text-success">
                                                                                                            <th colspan="3" class="text-end">
                                                                                                                        Diskon (<?= htmlspecialchars($order['voucher_code']) ?>)
                                                                                                            </th>
                                                                                                            <th class="text-end">- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></th>
                                                                                                </tr>
                                                                                    <?php endif; ?>
                                                                                    <tr>
                                                                                                <th colspan="3" class="text-end">Ongkos Kirim</th>
                                                                                                <th class="text-end">Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></th>
                                                                                    </tr>
                                                                                    <tr class="table-light">
                                                                                                <th colspan="3" class="text-end fs-5">Total Akhir</th>
                                                                                                <th class="text-end fs-5">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></th>
                                                                                    </tr>
                                                                        </tfoot>
                                                            </table>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>