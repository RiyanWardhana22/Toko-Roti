<?php
if (!isset($_GET['id'])) {
            echo "<div class='alert alert-danger'>ID Pesanan tidak ditemukan.</div>";
            return;
}
$order_id = (int)$_GET['id'];
$message = '';

$order_res = mysqli_query($conn, "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = $order_id");
$order = mysqli_fetch_assoc($order_res);

$site_settings_res = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'website_title'");
$website_name = mysqli_fetch_assoc($site_settings_res)['setting_value'] ?? 'Toko Anda';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
            $old_status = $order['status'];
            $new_status = mysqli_real_escape_string($conn, $_POST['status']);

            if ($new_status != $old_status) {
                        $query = "UPDATE orders SET status = ?";
                        $params = [$new_status];
                        $types = "s";

                        if ($new_status == 'Dikirim') {
                                    $query .= ", shipped_at = NOW()";
                        }
                        $query .= " WHERE id = ?";
                        $params[] = $order_id;
                        $types .= "i";

                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, $types, ...$params);

                        if (mysqli_stmt_execute($stmt)) {
                                    $order['status'] = $new_status;
                                    require_once __DIR__ . '/../../app/mailer.php';
                                    if ($new_status == 'Diproses' && $old_status == 'Menunggu Verifikasi') {
                                                $items_res_mail = mysqli_query($conn, "SELECT oi.quantity, oi.price, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
                                                $order_details_html = '<table class="order-details-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px"><thead><tr><th style="padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px;">Nama Item</th><th style="padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px;">Jumlah</th><th style="padding: 10px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px;">Harga</th></tr></thead><tbody>';
                                                while ($item = mysqli_fetch_assoc($items_res_mail)) {
                                                            $order_details_html .= '<tr><td style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">' . htmlspecialchars($item['product_name']) . '</td><td style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">' . $item['quantity'] . 'x</td><td style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">Rp ' . number_format($item['price'] * $item['quantity'], 0, ',', '.') . '</td></tr>';
                                                }
                                                if ($order['discount_amount'] > 0) {
                                                            $order_details_html .= '<tr><td colspan="2" style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">Diskon </td><td style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">- Rp ' . number_format($order['discount_amount'], 0, ',', '.') . '</td></tr>';
                                                }
                                                $order_details_html .= '<tr><td colspan="2" style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">Ongkos Kirim</td><td style="padding: 10px; text-align: left; border-bottom: 1px solid #eee">Rp ' . number_format($order['shipping_cost'], 0, ',', '.') . '</td></tr>';
                                                $order_details_html .= '</tbody></table>';

                                                $template_path = realpath(__DIR__ . '/../../templates/email/ereceipt_template.html');
                                                if (file_exists($template_path)) {
                                                            $email_body = file_get_contents($template_path);
                                                            $placeholders = [
                                                                        '{{customer_name}}' => $order['customer_name'],
                                                                        '{{tahun_ini}}' => date('Y'),
                                                                        '{{order_id}}' => $order_id,
                                                                        '{{status}}' => htmlspecialchars($order['status']),
                                                                        '{{order_date}}' => date('d F Y, H:i', strtotime($order['created_at'])),
                                                                        '{{total_amount}}' => number_format($order['total_amount'], 0, ',', '.'),
                                                                        '{{website_name}}' => $website_name,
                                                                        '{{payment_method}}' => htmlspecialchars($order['payment_method']),
                                                                        '{{shipping_method}}' => htmlspecialchars($order['shipping_method']),
                                                                        '{{order_details_table}}' => $order_details_html,
                                                            ];
                                                            $email_body = str_replace(array_keys($placeholders), array_values($placeholders), $email_body);
                                                            $subject = "Pembayaran Berhasil untuk Pesanan #" . $order_id;
                                                            send_email($order['customer_email'], $order['customer_name'], $subject, $email_body);
                                                            $message = "<div class='alert alert-success'>Status pesanan berhasil diperbarui dan E-Receipt telah dikirim.</div>";
                                                }
                                    } else {
                                                $template_path = realpath(__DIR__ . '/../../templates/email/status_update_template.html');
                                                if (file_exists($template_path)) {
                                                            $email_body = file_get_contents($template_path);
                                                            $order_detail_link = BASE_URL . 'order_detail_customer?id=' . $order_id;

                                                            $placeholders = [
                                                                        '{{customer_name}}'   => $order['customer_name'],
                                                                        '{{order_id}}'        => $order_id,
                                                                        '{{new_status}}'      => htmlspecialchars($new_status),
                                                                        '{{order_detail_link}}' => $order_detail_link,
                                                                        '{{website_name}}'    => $website_name,
                                                                        '{{current_year}}'    => date('Y'),
                                                            ];

                                                            $email_body = str_replace(array_keys($placeholders), array_values($placeholders), $email_body);
                                                            $subject = "Update Status Pesanan #" . $order_id . " - " . $website_name;
                                                            $emailSent = send_email($order['customer_email'], $order['customer_name'], $subject, $email_body);

                                                            if ($emailSent === true) {
                                                                        $message = "<div class='alert alert-success'>Status pesanan berhasil diperbarui dan notifikasi telah dikirim ke pelanggan.</div>";
                                                            } else {
                                                                        $message = "<div class='alert alert-warning'>Status pesanan diperbarui, tetapi notifikasi email GAGAL dikirim. Error: " . htmlspecialchars($emailSent) . "</div>";
                                                            }
                                                } else {
                                                            $message = "<div class='alert alert-danger'>Status pesanan diperbarui, tetapi template email notifikasi tidak ditemukan.</div>";
                                                }
                                    }
                        } else {
                                    $message = "<div class='alert alert-danger'>Gagal memperbarui status pesanan.</div>";
                        }
            } else {
                        $message = "<div class='alert alert-info'>Tidak ada perubahan status yang dilakukan.</div>";
            }
}

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
                                                            <strong>Telepon:</strong> <?= htmlspecialchars($order['customer_phone']) ?><br>
                                                            <strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['payment_method']) ?><br>
                                                            <strong>Metode Pengiriman:</strong> <?= htmlspecialchars($order['shipping_method']) ?>
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
                                                <div class="d-flex align-items-center">
                                                            <a href="<?= BASE_URL ?>admin?page=invoice&id=<?= $order_id ?>" target="_blank" class="btn btn-secondary btn-sm me-2"><i class="fa-solid fa-print"></i></a>
                                                            <form method="POST" action="" class="mb-0">
                                                                        <div class="input-group">
                                                                                    <select class="form-select" name="status" style="width: 150px;">
                                                                                                <?php foreach ($statuses as $status): ?>
                                                                                                            <option value="<?= $status ?>" <?= ($order['status'] == $status) ? 'selected' : '' ?>><?= $status ?></option>
                                                                                                <?php endforeach; ?>
                                                                                    </select>
                                                                                    <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                                                        </div>
                                                            </form>
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
                                                                                    <?php mysqli_data_seek($items_res, 0);
                                                                                    while ($item = mysqli_fetch_assoc($items_res)): ?>
                                                                                                <tr>
                                                                                                            <td>
                                                                                                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                                                                                                        <?php if (!empty($item['customization_details'])): ?>
                                                                                                                                    <p class="mb-0 mt-1"><small class="text-muted"><em>Catatan: "<?= htmlspecialchars($item['customization_details']) ?>"</em></small></p>
                                                                                                                        <?php endif; ?>
                                                                                                            </td>
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
                                                                                                            <th colspan="3" class="text-end">Voucher</th>
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