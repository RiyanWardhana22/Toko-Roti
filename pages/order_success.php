<?php
if (!isset($_GET['order_id'])) {
            header('Location: ' . BASE_URL);
            exit();
}
$order_id = (int)$_GET['order_id'];

$order_res = mysqli_query($conn, "SELECT total_amount FROM orders WHERE id = $order_id");
$order = mysqli_fetch_assoc($order_res);
$total_amount = $order['total_amount'] ?? 0;
?>

<div class="container ">
            <div class="row justify-content-center">
                        <div class="col-lg-8">
                                    <div class="order-success-section text-center">
                                                <div class="success-icon">
                                                            <i class="fas fa-check-circle"></i>
                                                </div>
                                                <h1 class="mb-3">Pesanan Anda Berhasil!</h1>
                                                <p class="lead">Terima kasih telah berbelanja di <?= htmlspecialchars($site_settings['website_title']) ?></p>

                                                <div class="order-details">
                                                            <h4 class="mb-3">Detail Pesanan</h4>
                                                            <div class="order-number">#<?= $order_id ?></div>
                                                </div>

                                                <div class="next-steps text-start">
                                                            <h4 class="text-center mb-4">Langkah Selanjutnya</h4>

                                                            <div class="step">
                                                                        <div class="step-number">1</div>
                                                                        <div class="step-content">
                                                                                    <h5>Konfirmasi Pembayaran</h5>
                                                                                    <p>Lakukan pembayaran dan konfirmasi melalui halaman akun Anda.</p>
                                                                        </div>
                                                            </div>

                                                            <div class="step">
                                                                        <div class="step-number">2</div>
                                                                        <div class="step-content">
                                                                                    <h5>Pesanan Diproses</h5>
                                                                                    <p>Kami akan mempersiapkan pesanan Anda.</p>
                                                                        </div>
                                                            </div>

                                                            <div class="step">
                                                                        <div class="step-number">3</div>
                                                                        <div class="step-content">
                                                                                    <h5>Pesanan Dikirim</h5>
                                                                                    <p>Anda akan menerima notifikasi melalui email ketika pesanan sudah dikirim.</p>
                                                                        </div>
                                                            </div>
                                                </div>

                                                <div class="action-buttons">
                                                            <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-primary px-4 py-2">
                                                                        <i class="fas fa-receipt me-2"></i> Konfirmasi Pembayaran
                                                            </a>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>