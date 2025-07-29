<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan');
            exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$order_stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan');
            exit();
}

$items_stmt = mysqli_prepare($conn, "SELECT oi.*, p.name as product_name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);

include 'parts/header.php';
?>

<div class="container py-3">
            <div class="row">
                        <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-outline-primary">
                                                            <i class="fas fa-arrow-left me-2"></i> Kembali
                                                </a>
                                    </div>
                        </div>
            </div>

            <div class="row g-4">
                        <div class="col-lg-8">
                                    <div class="order-detail-container">
                                                <div class="order-header">
                                                            <h3>Detail Produk</h3>
                                                </div>

                                                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                                            <div class="product-card-detail">
                                                                        <div class="row g-0">
                                                                                    <div class="col-md-2">
                                                                                                <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid product-image" alt="<?= htmlspecialchars($item['product_name']) ?>">
                                                                                    </div>
                                                                                    <div class="col-md-10 p-3">
                                                                                                <div class="card-body">
                                                                                                            <h5 class="product-name"><?= htmlspecialchars($item['product_name']) ?></h5>
                                                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                                                        <div>
                                                                                                                                    <span class="text-muted"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                                                                                                                        </div>
                                                                                                                        <div class="product-price">
                                                                                                                                    Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                </div>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                <?php endwhile; ?>

                                                <div class="order-header mt-5">
                                                            <h3>Informasi Pengiriman</h3>
                                                </div>

                                                <div class="order-info-card">
                                                            <h5 class="order-info-title">Alamat Pengiriman</h5>
                                                            <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                                                            <hr>
                                                            <div class="row">
                                                                        <div class="col-md-6">
                                                                                    <h5 class="order-info-title">Metode Pengiriman</h5>
                                                                                    <p><?= htmlspecialchars($order['shipping_method']) ?></p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                                    <h5 class="order-info-title">Biaya Pengiriman</h5>
                                                                                    <p>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></p>
                                                                        </div>
                                                            </div>
                                                </div>

                                                <!-- Order Timeline -->
                                                <div class="order-header mt-5">
                                                            <h3>Status Pesanan</h3>
                                                </div>

                                                <div class="timeline">
                                                            <div class="timeline-item">
                                                                        <h6>Pesanan Dibuat</h6>
                                                                        <p class="timeline-date"><?= date('d F Y, H:i', strtotime($order['created_at'])) ?></p>
                                                                        <p>Pesanan telah diterima dan menunggu konfirmasi pembayaran.</p>
                                                            </div>

                                                            <?php if ($order['status'] != 'Menunggu Pembayaran'): ?>
                                                                        <div class="timeline-item">
                                                                                    <h6>Pembayaran DiKonfirmasi</h6>
                                                                                    <p>Pembayaran telah diverifikasi, selanjutnya menunggu pesanan anda di proses.</p>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <?php if ($order['status'] == 'Diproses' || $order['status'] == 'Dikirim' || $order['status'] == 'Selesai'): ?>
                                                                        <div class="timeline-item">
                                                                                    <h6>Pesanan Diproses</h6>
                                                                                    <p>Pesanan sedang disiapkan oleh tim kami.</p>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <?php if ($order['status'] == 'Dikirim' || $order['status'] == 'Selesai'): ?>
                                                                        <div class="timeline-item">
                                                                                    <h6>Pesanan Dikirim</h6>
                                                                                    <p>Pesanan telah dikirim ke alamat tujuan.</p>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <?php if ($order['status'] == 'Selesai'): ?>
                                                                        <div class="timeline-item">
                                                                                    <h6>Pesanan Selesai</h6>
                                                                                    <p>Pesanan tiba di alamat tujuan. Diterima oleh Yang bersangkutan.</p>
                                                                        </div>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-lg-4">
                                    <div class="order-detail-container">
                                                <div class="order-header">
                                                            <h3>Ringkasan Pesanan</h3>
                                                </div>

                                                <div class="mb-4">
                                                            <div class="summary-item d-flex justify-content-between">
                                                                        <span>Subtotal Produk</span>
                                                                        <span>Rp <?= number_format($order['total_amount'] - $order['shipping_cost'], 0, ',', '.') ?></span>
                                                            </div>
                                                            <div class="summary-item d-flex justify-content-between">
                                                                        <span>Ongkos Kirim</span>
                                                                        <span>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></span>
                                                            </div>
                                                            <?php if ($order['discount_amount'] > 0): ?>
                                                                        <div class="summary-item d-flex justify-content-between">
                                                                                    <span>Voucher</span>
                                                                                    <span>- Rp <?= number_format($order['discount_amount'], 0, ',', '.') ?></span>
                                                                        </div>
                                                            <?php endif; ?>
                                                            <div class="summary-item d-flex justify-content-between summary-total">
                                                                        <span>Total Pembayaran</span>
                                                                        <span>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                                                            </div>
                                                </div>
                                                <div class="mb-3">
                                                            <?php if ($order['status'] == 'Menunggu Pembayaran'): ?>
                                                                        <a href="<?= BASE_URL ?>payment_confirmation?id=<?= $order['id'] ?>" class="btn btn-primary w-100 mb-3">
                                                                                    Konfirmasi Pembayaran
                                                                        </a>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>

<?php
include 'parts/footer.php';
?>