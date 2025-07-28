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
$order_stmt = mysqli_prepare($conn, "SELECT id, total_amount, status, created_at FROM orders WHERE id = ? AND user_id = ? AND status = 'Menunggu Pembayaran'");
mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);
if (!$order) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan&error=invalid_order');
            exit();
}
$payment_methods_res = mysqli_query($conn, "SELECT * FROM payment_methods WHERE is_active = 1 AND method_name NOT LIKE '%Bayar di Toko%'");
$confirm_check = mysqli_query($conn, "SELECT id FROM payment_confirmations WHERE order_id = $order_id");
$already_confirmed = mysqli_num_rows($confirm_check) > 0;

include 'parts/header.php';
?>

<main>
            <div class="container">
                        <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                                <div class="payment-confirmation-container">
                                                            <h2 class=" text-center">KONFIRMASI PEMBAYARAN</h2>
                                                            <div class="my-4">
                                                                        <?php
                                                                        $deadline = strtotime($order['created_at']) + (12 * 3600);
                                                                        ?>
                                                                        <div class="mb-3 border-top pt-3">
                                                                                    <h5 class="">Selesaikan pembayaran dalam:</h5>
                                                                                    <span class="fs-6 text-danger countdown-timer" data-deadline="<?= date('Y-m-d H:i:s', $deadline) ?>">
                                                                                                Menghitung...
                                                                                    </span>
                                                                        </div>
                                                                        <h5 class="mb-3">Detail Pesanan #<?= $order['id'] ?></h5>
                                                                        <div>
                                                                                    <span class="text-muted">Total Tagihan:</span>
                                                                                    <span class="fs-6 fw-bold text-success">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                                                                        </div>

                                                            </div>

                                                            <?php if ($already_confirmed): ?>
                                                                        <div class="alert alert-success text-center">
                                                                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                                                    <h5 class="alert-heading">Konfirmasi Diterima!</h5>
                                                                                    <p>Terima kasih, kami telah menerima konfirmasi pembayaran Anda. Tim kami akan segera melakukan verifikasi.</p>
                                                                                    <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-primary mt-2">Kembali ke Riwayat Pesanan</a>
                                                                        </div>
                                                            <?php else: ?>
                                                                        <div class="mb-4">
                                                                                    <h5 class="mb-3">Pilih Metode Pembayaran</h5>
                                                                                    <p class="text-muted">Klik pada salah satu metode untuk melihat detail instruksi pembayaran.</p>

                                                                                    <div class="payment-methods-grid">
                                                                                                <?php mysqli_data_seek($payment_methods_res, 0);
                                                                                                while ($method = mysqli_fetch_assoc($payment_methods_res)): ?>
                                                                                                            <a href="#" class="payment-method-box" data-bs-toggle="modal" data-bs-target="#paymentModal-<?= $method['id'] ?>">
                                                                                                                        <?php if ($method['logo_url']): ?>
                                                                                                                                    <img src="<?= BASE_URL ?>assets/images/logos/<?= $method['logo_url'] ?>" alt="<?= htmlspecialchars($method['method_name']) ?>">
                                                                                                                        <?php endif; ?>
                                                                                                                        <div class="method-name"><?= htmlspecialchars($method['method_name']) ?></div>
                                                                                                            </a>
                                                                                                <?php endwhile; ?>
                                                                                    </div>

                                                                                    <div class="text-center mt-2" id="toggle-methods-container" style="display: none;">
                                                                                                <a href="#" id="show-more-methods" class="text-decoration-none fw-bold">Lihat Lainnya <i class="fas fa-chevron-down ms-1 small"></i></a>
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
                                                                                                                        <div class="modal-body text-center">
                                                                                                                                    <?php if (!empty($method['qris_image_url'])): ?>
                                                                                                                                                <img src="<?= BASE_URL ?>assets/images/qris/<?= $method['qris_image_url'] ?>" alt="QRIS Code" class="img-fluid my-2" style="max-width: 280px;">
                                                                                                                                    <?php else: ?>
                                                                                                                                                <p class="text-muted mb-3">Silakan lakukan transfer ke rekening berikut:</p>
                                                                                                                                                <div class="bg-light p-3 rounded">
                                                                                                                                                            <p class="fw-bold fs-5 mb-0"><?= nl2br(htmlspecialchars($method['account_details'])) ?></p>
                                                                                                                                                </div>
                                                                                                                                    <?php endif; ?>
                                                                                                                                    <hr>
                                                                                                                                    <p class="mt-3 small">Setelah melakukan pembayaran, lanjutkan mengisi form konfirmasi di bawah.</p>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                </div>
                                                                                    </div>
                                                                        <?php endwhile; ?>

                                                                        <div class="mb-4">
                                                                                    <h5 class="mb-3">Isi Form Konfirmasi</h5>
                                                                                    <p class="text-muted">Setelah melakukan pembayaran, mohon lengkapi form berikut:</p>
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
                                                                                                                                    <div class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                                                                                                                    <div>Klik untuk mengunggah bukti transfer</div>
                                                                                                                                    <div class="file-name" id="file-name">Format: JPG, PNG (Maks. 2MB)</div>
                                                                                                                        </label>
                                                                                                            </div>
                                                                                                </div>
                                                                                                <div class="d-grid gap-2">
                                                                                                            <button type="submit" class="btn btn-primary py-3">Kirim Konfirmasi</button>
                                                                                                </div>
                                                                                    </form>
                                                                        </div>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>

<?php include 'parts/footer.php'; ?>
<script>
            document.addEventListener('DOMContentLoaded', function() {
                        const grid = document.querySelector('.payment-methods-grid');
                        const showMoreBtn = document.getElementById('show-more-methods');
                        const toggleContainer = document.getElementById('toggle-methods-container');

                        function checkOverflow() {
                                    const isOverflowing = grid.scrollHeight > grid.clientHeight;

                                    if (isOverflowing) {
                                                toggleContainer.style.display = 'block';
                                    } else {
                                                toggleContainer.style.display = 'none';
                                    }
                        }

                        checkOverflow();
                        window.addEventListener('resize', checkOverflow);
                        if (showMoreBtn) {
                                    showMoreBtn.addEventListener('click', function(e) {
                                                e.preventDefault();
                                                grid.classList.toggle('show-all');

                                                if (grid.classList.contains('show-all')) {
                                                            this.innerHTML = 'Tampilkan Lebih Sedikit <i class="fas fa-chevron-up ms-1 small"></i>';
                                                } else {
                                                            this.innerHTML = 'Lihat Lainnya <i class="fas fa-chevron-down ms-1 small"></i>';
                                                }
                                    });
                        }

                        const proofImageInput = document.getElementById('proof_image');
                        if (proofImageInput) {
                                    proofImageInput.addEventListener('change', function(e) {
                                                const fileName = e.target.files[0] ? e.target.files[0].name : 'Format: JPG, PNG (Maks. 2MB)';
                                                document.getElementById('file-name').textContent = fileName;
                                    });
                        }
            });
</script>