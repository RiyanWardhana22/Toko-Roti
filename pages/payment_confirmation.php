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

$order_stmt = mysqli_prepare($conn, "SELECT id, total_amount, status FROM orders WHERE id = ? AND user_id = ? AND status = 'Menunggu Pembayaran'");
mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan&error=invalid_order');
            exit();
}

$payment_methods_res = mysqli_query($conn, "SELECT * FROM payment_methods WHERE is_active = 1");
$confirm_check = mysqli_query($conn, "SELECT id FROM payment_confirmations WHERE order_id = $order_id");
$already_confirmed = mysqli_num_rows($confirm_check) > 0;

include 'parts/header.php';
?>

<div class="container">
            <div class="row justify-content-center">
                        <div class="col-lg-8">
                                    <div class="payment-confirmation-container">
                                                <h2 class="mb-4 text-center">Konfirmasi Pembayaran</h2>

                                                <div class="order-summary">
                                                            <h5 class="mb-3">Detail Pesanan #<?= $order['id'] ?></h5>
                                                            <div class="mt-2">
                                                                        <span class="text-muted">Total Tagihan:</span>
                                                                        <span class="fs-6 fw-bold text-success">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                                                            </div>
                                                </div>

                                                <?php if ($already_confirmed): ?>
                                                            <div class="alert alert-success">
                                                                        <div class="d-flex align-items-center">
                                                                                    <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                                                                                    <div>
                                                                                                <h5 class="alert-heading mb-2">Konfirmasi Pembayaran Diterima!</h5>
                                                                                                <p class="mb-0">Terima kasih telah melakukan konfirmasi pembayaran. Tim kami akan segera memverifikasi pembayaran Anda.</p>
                                                                                    </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="text-center mt-3">
                                                                                    <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-primary px-4">
                                                                                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Riwayat Pesanan
                                                                                    </a>
                                                                        </div>
                                                            </div>
                                                <?php else: ?>
                                                            <div class="mb-4">
                                                                        <h5 class="mb-3">Pilih Metode Pembayaran</h5>
                                                                        <p class="text-muted">Klik pada salah satu metode untuk melihat detail instruksi pembayaran.</p>

                                                                        <div class="payment-methods-grid">
                                                                                    <?php while ($method = mysqli_fetch_assoc($payment_methods_res)): ?>
                                                                                                <a href="#" class="payment-method-box" data-bs-toggle="modal" data-bs-target="#paymentModal-<?= $method['id'] ?>">
                                                                                                            <?php if ($method['logo_url']): ?>
                                                                                                                        <img src="<?= BASE_URL ?>assets/images/logos/<?= $method['logo_url'] ?>" alt="<?= htmlspecialchars($method['method_name']) ?>">
                                                                                                            <?php endif; ?>
                                                                                                            <div class="method-name"><?= htmlspecialchars($method['method_name']) ?></div>
                                                                                                </a>
                                                                                    <?php endwhile; ?>
                                                                        </div>
                                                            </div>


                                                            <?php
                                                            mysqli_data_seek($payment_methods_res, 0);
                                                            while ($method = mysqli_fetch_assoc($payment_methods_res)):
                                                            ?>
                                                                        <div class="modal fade payment-modal" id="paymentModal-<?= $method['id'] ?>" tabindex="-1" aria-labelledby="paymentModalLabel-<?= $method['id'] ?>" aria-hidden="true">
                                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                                                <div class="modal-content">
                                                                                                            <div class="modal-header">
                                                                                                                        <div class="modal-title-wrapper">
                                                                                                                                    <?php if ($method['logo_url']): ?>
                                                                                                                                                <img src="<?= BASE_URL ?>assets/images/logos/<?= $method['logo_url'] ?>" alt="<?= htmlspecialchars($method['method_name']) ?>">
                                                                                                                                    <?php endif; ?>
                                                                                                                                    <h5 class="modal-title" id="paymentModalLabel-<?= $method['id'] ?>"><?= htmlspecialchars($method['method_name']) ?></h5>
                                                                                                                        </div>
                                                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                                            </div>
                                                                                                            <div class="modal-body">
                                                                                                                        <p class="text-muted mb-3">Silakan lakukan transfer ke rekening berikut:</p>
                                                                                                                        <p class="fw-bold"><?= nl2br(htmlspecialchars($method['account_details'])) ?></p>
                                                                                                                        <hr>
                                                                                                                        <p class="mt-4 small">Setelah melakukan transfer, mohon lanjutkan mengisi form konfirmasi di bawah halaman ini.</p>
                                                                                                            </div>
                                                                                                </div>
                                                                                    </div>
                                                                        </div>
                                                            <?php endwhile; ?>

                                                            <div class="mb-4">
                                                                        <h5 class="mb-3">Form Konfirmasi Pembayaran</h5>
                                                                        <p class="text-muted">Setelah melakukan transfer, mohon lengkapi form berikut:</p>

                                                                        <form action="<?= BASE_URL ?>app/payment_action.php" method="POST" enctype="multipart/form-data">
                                                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                                                                                    <div class="row">
                                                                                                <div class="col-md-6 mb-3">
                                                                                                            <label class="form-label">Transfer dari Bank/E-Wallet</label>
                                                                                                            <input type="text" class="form-control" name="bank_name" placeholder="Contoh: BCA, GoPay, OVO" required>
                                                                                                </div>

                                                                                                <div class="col-md-6 mb-3">
                                                                                                            <label class="form-label">Nama Pemilik Rekening</label>
                                                                                                            <input type="text" class="form-control" name="account_holder" placeholder="Nama sesuai rekening/e-wallet" required>
                                                                                                </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                                <div class="col-md-6 mb-3">
                                                                                                            <label class="form-label">Jumlah Transfer</label>
                                                                                                            <div class="input-group">
                                                                                                                        <span class="input-group-text">Rp</span>
                                                                                                                        <input type="number" class="form-control" name="transfer_amount" value="<?= (int)$order['total_amount'] ?>" required>
                                                                                                            </div>
                                                                                                </div>

                                                                                                <div class="col-md-6 mb-3">
                                                                                                            <label class="form-label">Tanggal Transfer</label>
                                                                                                            <input type="date" class="form-control" name="transfer_date" required>
                                                                                                </div>
                                                                                    </div>

                                                                                    <div class="mb-4">
                                                                                                <label class="form-label">Upload Bukti Transfer</label>
                                                                                                <div class="file-upload-wrapper">
                                                                                                            <input type="file" class="file-upload-input" id="proof_image" name="proof_image" accept="image/*" required>
                                                                                                            <label for="proof_image" class="file-upload-label">
                                                                                                                        <div class="file-upload-icon">
                                                                                                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                                                                                        </div>
                                                                                                                        <div>Klik untuk mengunggah bukti transfer</div>
                                                                                                                        <div class="file-name" id="file-name">Format: JPG, PNG (Maks. 2MB)</div>
                                                                                                            </label>
                                                                                                </div>
                                                                                    </div>

                                                                                    <div class="d-grid gap-2">
                                                                                                <button type="submit" class="btn btn-primary py-3">Konfirmasi Pembayaran</button>
                                                                                    </div>
                                                                        </form>
                                                            </div>
                                                <?php endif; ?>
                                    </div>
                        </div>
            </div>
</div>

<?php
include 'parts/footer.php';
?>

<script>
            document.getElementById('proof_image').addEventListener('change', function(e) {
                        const fileName = e.target.files[0] ? e.target.files[0].name : 'Format: JPG, PNG (Maks. 2MB)';
                        document.getElementById('file-name').textContent = fileName;
            });
</script>