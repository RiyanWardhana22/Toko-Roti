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

<div class="container py-5">
            <div class="row">
                        <div class="col-lg-8 mx-auto">
                                    <div class="card">
                                                <div class="card-header">
                                                            <h3>Konfirmasi Pembayaran</h3>
                                                </div>
                                                <div class="card-body">
                                                            <p>Untuk Pesanan: <strong>#<?= $order['id'] ?></strong><br>
                                                                        Total Tagihan: <strong class="text-danger">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></p>
                                                            <hr>

                                                            <?php if ($already_confirmed): ?>
                                                                        <div class="alert alert-success">
                                                                                    <h4 class="alert-heading">Terima Kasih!</h4>
                                                                                    <p>Anda sudah melakukan konfirmasi pembayaran untuk pesanan ini. Kami akan segera memverifikasi pembayaran Anda.</p>
                                                                                    <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-primary">Kembali ke Riwayat Pesanan</a>
                                                                        </div>
                                                            <?php else: ?>
                                                                        <p>Silakan transfer ke salah satu metode pembayaran berikut:</p>

                                                                        <div class="accordion mb-3" id="paymentMethodsAccordion">
                                                                                    <?php while ($method = mysqli_fetch_assoc($payment_methods_res)): ?>
                                                                                                <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="heading-<?= $method['id'] ?>">
                                                                                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $method['id'] ?>">
                                                                                                                                    <?php if ($method['logo_url']): ?>
                                                                                                                                                <img src="<?= BASE_URL ?>assets/images/logos/<?= $method['logo_url'] ?>" height="25" class="me-2">
                                                                                                                                    <?php endif; ?>
                                                                                                                                    <strong><?= htmlspecialchars($method['method_name']) ?></strong>
                                                                                                                        </button>
                                                                                                            </h2>
                                                                                                            <div id="collapse-<?= $method['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#paymentMethodsAccordion">
                                                                                                                        <div class="accordion-body">
                                                                                                                                    <?= nl2br(htmlspecialchars($method['account_details'])) ?>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                </div>
                                                                                    <?php endwhile; ?>
                                                                        </div>

                                                                        <p>Setelah melakukan transfer, mohon isi form di bawah ini.</p>

                                                                        <form action="<?= BASE_URL ?>app/payment_action.php" method="POST" enctype="multipart/form-data">
                                                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                                                    <div class="mb-3"><label class="form-label">Transfer dari Bank/E-Wallet</label><input type="text" class="form-control" name="bank_name" placeholder="Cth: BCA/GoPay" required></div>
                                                                                    <div class="mb-3"><label class="form-label">Nama Pemilik Rekening</label><input type="text" class="form-control" name="account_holder" required></div>
                                                                                    <div class="mb-3"><label class="form-label">Jumlah Transfer</label><input type="number" class="form-control" name="transfer_amount" value="<?= (int)$order['total_amount'] ?>" required></div>
                                                                                    <div class="mb-3"><label class="form-label">Tanggal Transfer</label><input type="date" class="form-control" name="transfer_date" required></div>
                                                                                    <div class="mb-3"><label class="form-label">Upload Bukti Transfer</label><input type="file" class="form-control" name="proof_image" required></div>
                                                                                    <button type="submit" class="btn btn-primary">Kirim Konfirmasi</button>
                                                                        </form>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>

<?php
// Muat footer
include 'parts/footer.php';
?>