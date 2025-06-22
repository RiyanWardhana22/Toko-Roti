<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
            redirect('/pages/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$cart_items = getCartItems($_SESSION['user_id']);
$subtotal = calculateCartSubtotal($cart_items);
$user = getUserById($_SESSION['user_id']);

if (empty($cart_items)) {
            redirect('/pages/cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shipping_address = trim($_POST['shipping_address']);
            $shipping_method = $_POST['shipping_method'];
            $payment_method = $_POST['payment_method'];
            $notes = trim($_POST['notes']);

            $errors = [];

            if (empty($shipping_address)) {
                        $errors['shipping_address'] = 'Alamat pengiriman harus diisi';
            }

            if (empty($shipping_method)) {
                        $errors['shipping_method'] = 'Metode pengiriman harus dipilih';
            }

            if (empty($payment_method)) {
                        $errors['payment_method'] = 'Metode pembayaran harus dipilih';
            }

            if (empty($errors)) {
                        $order_id = processCheckout(
                                    $_SESSION['user_id'],
                                    $shipping_address,
                                    $shipping_method,
                                    $payment_method,
                                    $subtotal,
                                    $notes
                        );

                        if ($order_id) {
                                    redirect("/pages/order-confirmation.php?id=$order_id");
                        } else {
                                    $errors['general'] = 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.';
                        }
            }
}
?>

<section class="py-5">
            <div class="container">
                        <h2 class="mb-5">Checkout</h2>

                        <?php if (isset($errors['general'])): ?>
                                    <div class="alert alert-danger"><?= $errors['general'] ?></div>
                        <?php endif; ?>

                        <form method="POST" id="checkout-form">
                                    <div class="row">
                                                <div class="col-md-7">
                                                            <div class="card mb-4">
                                                                        <div class="card-header bg-primary text-white">
                                                                                    <h5 class="mb-0">Informasi Pengiriman</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Nama Lengkap</label>
                                                                                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Email</label>
                                                                                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Nomor Telepon</label>
                                                                                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                                                                                                <textarea class="form-control <?= isset($errors['shipping_address']) ? 'is-invalid' : '' ?>"
                                                                                                            name="shipping_address" rows="3" required><?= isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : htmlspecialchars($user['address']) ?></textarea>
                                                                                                <?php if (isset($errors['shipping_address'])): ?>
                                                                                                            <div class="invalid-feedback"><?= $errors['shipping_address'] ?></div>
                                                                                                <?php endif; ?>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Metode Pengiriman <span class="text-danger">*</span></label>
                                                                                                <select class="form-select <?= isset($errors['shipping_method']) ? 'is-invalid' : '' ?>" name="shipping_method" required>
                                                                                                            <option value="">Pilih metode pengiriman</option>
                                                                                                            <option value="pickup" <?= (isset($_POST['shipping_method']) && $_POST['shipping_method'] === 'pickup') ? 'selected' : '' ?>>Ambil di Toko</option>
                                                                                                            <option value="gosend" <?= (isset($_POST['shipping_method']) && $_POST['shipping_method'] === 'gosend') ? 'selected' : '' ?>>GoSend (Same Day)</option>
                                                                                                            <option value="grab" <?= (isset($_POST['shipping_method']) && $_POST['shipping_method'] === 'grab') ? 'selected' : '' ?>>Grab Express</option>
                                                                                                </select>
                                                                                                <?php if (isset($errors['shipping_method'])): ?>
                                                                                                            <div class="invalid-feedback"><?= $errors['shipping_method'] ?></div>
                                                                                                <?php endif; ?>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Catatan (Opsional)</label>
                                                                                                <textarea class="form-control" name="notes" rows="2"><?= isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '' ?></textarea>
                                                                                                <small class="text-muted">Contoh: Warna kue, pesan khusus, dll.</small>
                                                                                    </div>
                                                                        </div>
                                                            </div>

                                                            <div class="card">
                                                                        <div class="card-header bg-primary text-white">
                                                                                    <h5 class="mb-0">Metode Pembayaran</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Pilih Metode Pembayaran <span class="text-danger">*</span></label>
                                                                                                <select class="form-select <?= isset($errors['payment_method']) ? 'is-invalid' : '' ?>" name="payment_method" required>
                                                                                                            <option value="">Pilih metode pembayaran</option>
                                                                                                            <option value="bca" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'bca') ? 'selected' : '' ?>>Transfer Bank - BCA</option>
                                                                                                            <option value="mandiri" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'mandiri') ? 'selected' : '' ?>>Transfer Bank - Mandiri</option>
                                                                                                            <option value="bri" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'bri') ? 'selected' : '' ?>>Transfer Bank - BRI</option>
                                                                                                            <option value="cod" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cod') ? 'selected' : '' ?>>Cash on Delivery (COD)</option>
                                                                                                </select>
                                                                                                <?php if (isset($errors['payment_method'])): ?>
                                                                                                            <div class="invalid-feedback"><?= $errors['payment_method'] ?></div>
                                                                                                <?php endif; ?>
                                                                                    </div>

                                                                                    <!-- Payment Instructions (shown based on selection) -->
                                                                                    <div id="payment-instructions" class="mt-4 p-3 bg-light rounded d-none">
                                                                                                <h6>Instruksi Pembayaran</h6>
                                                                                                <div id="bca-instructions" class="payment-method-instructions">
                                                                                                            <p>Silakan transfer ke rekening BCA berikut:</p>
                                                                                                            <p><strong>Nomor Rekening:</strong> 1234567890</p>
                                                                                                            <p><strong>Atas Nama:</strong> Toko Roti Enak</p>
                                                                                                            <p>Total yang harus dibayar: <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></p>
                                                                                                            <p>Setelah melakukan pembayaran, harap konfirmasi dengan mengirimkan bukti transfer melalui WhatsApp ke 08123456789.</p>
                                                                                                </div>
                                                                                                <div id="mandiri-instructions" class="payment-method-instructions">
                                                                                                            <p>Silakan transfer ke rekening Mandiri berikut:</p>
                                                                                                            <p><strong>Nomor Rekening:</strong> 0987654321</p>
                                                                                                            <p><strong>Atas Nama:</strong> Toko Roti Enak</p>
                                                                                                            <p>Total yang harus dibayar: <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></p>
                                                                                                            <p>Setelah melakukan pembayaran, harap konfirmasi dengan mengirimkan bukti transfer melalui WhatsApp ke 08123456789.</p>
                                                                                                </div>
                                                                                                <div id="bri-instructions" class="payment-method-instructions">
                                                                                                            <p>Silakan transfer ke rekening BRI berikut:</p>
                                                                                                            <p><strong>Nomor Rekening:</strong> 5678901234</p>
                                                                                                            <p><strong>Atas Nama:</strong> Toko Roti Enak</p>
                                                                                                            <p>Total yang harus dibayar: <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></p>
                                                                                                            <p>Setelah melakukan pembayaran, harap konfirmasi dengan mengirimkan bukti transfer melalui WhatsApp ke 08123456789.</p>
                                                                                                </div>
                                                                                                <div id="cod-instructions" class="payment-method-instructions">
                                                                                                            <p>Anda akan membayar secara tunai ketika pesanan diterima.</p>
                                                                                                            <p>Pastikan Anda menyiapkan uang pas sebesar <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>.</p>
                                                                                                </div>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                </div>

                                                <div class="col-md-5">
                                                            <div class="card mb-4">
                                                                        <div class="card-header bg-primary text-white">
                                                                                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                                    <div class="table-responsive">
                                                                                                <table class="table">
                                                                                                            <thead>
                                                                                                                        <tr>
                                                                                                                                    <th>Produk</th>
                                                                                                                                    <th>Subtotal</th>
                                                                                                                        </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                        <?php foreach ($cart_items as $item): ?>
                                                                                                                                    <tr>
                                                                                                                                                <td>
                                                                                                                                                            <?= $item['name'] ?>
                                                                                                                                                            <span class="text-muted">× <?= $item['quantity'] ?></span>
                                                                                                                                                            <?php if ($item['variant_name']): ?>
                                                                                                                                                                        <br><small class="text-muted">Varian: <?= $item['variant_name'] ?></small>
                                                                                                                                                            <?php endif; ?>
                                                                                                                                                </td>
                                                                                                                                                <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                                                                                                                                    </tr>
                                                                                                                        <?php endforeach; ?>
                                                                                                            </tbody>
                                                                                                            <tfoot>
                                                                                                                        <tr>
                                                                                                                                    <th>Subtotal</th>
                                                                                                                                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                                                                                                        </tr>
                                                                                                                        <tr>
                                                                                                                                    <th>Ongkos Kirim</th>
                                                                                                                                    <td>
                                                                                                                                                <?php if (isset($_POST['shipping_method']) && $_POST['shipping_method'] === 'pickup'): ?>
                                                                                                                                                            Rp 0 (Ambil di Toko)
                                                                                                                                                <?php else: ?>
                                                                                                                                                            Akan dihitung
                                                                                                                                                <?php endif; ?>
                                                                                                                                    </td>
                                                                                                                        </tr>
                                                                                                                        <tr class="fw-bold">
                                                                                                                                    <th>Total</th>
                                                                                                                                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                                                                                                        </tr>
                                                                                                            </tfoot>
                                                                                                </table>
                                                                                    </div>

                                                                                    <div class="form-check mb-3">
                                                                                                <input class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>"
                                                                                                            type="checkbox" id="agree-terms" name="agree_terms" required>
                                                                                                <label class="form-check-label" for="agree-terms">
                                                                                                            Saya telah membaca dan menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Syarat & Ketentuan</a>
                                                                                                </label>
                                                                                                <?php if (isset($errors['terms'])): ?>
                                                                                                            <div class="invalid-feedback"><?= $errors['terms'] ?></div>
                                                                                                <?php endif; ?>
                                                                                    </div>

                                                                                    <button type="submit" class="btn btn-primary w-100 py-2">
                                                                                                Buat Pesanan
                                                                                    </button>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        </form>
            </div>
</section>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                                    <div class="modal-header">
                                                <h5 class="modal-title">Syarat & Ketentuan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                                <h6>Kebijakan Pengiriman</h6>
                                                <p>1. Pesanan akan diproses dalam 1-2 hari kerja setelah pembayaran dikonfirmasi.</p>
                                                <p>2. Untuk pengiriman same day, pesanan harus dilakukan sebelum jam 12.00 WIB.</p>
                                                <p>3. Biaya pengiriman akan ditanggung oleh pembeli.</p>

                                                <h6 class="mt-4">Kebijakan Pembayaran</h6>
                                                <p>1. Pembayaran harus dilakukan dalam waktu 24 jam setelah pesanan dibuat.</p>
                                                <p>2. Jika pembayaran tidak dilakukan dalam waktu yang ditentukan, pesanan akan dibatalkan secara otomatis.</p>

                                                <h6 class="mt-4">Kebijakan Pengembalian</h6>
                                                <p>1. Produk makanan tidak dapat dikembalikan atau ditukar.</p>
                                                <p>2. Jika ada masalah dengan pesanan, harap hubungi kami dalam waktu 2 jam setelah penerimaan.</p>
                                    </div>
                                    <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                        </div>
            </div>
</div>

<script>
            document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
                        const paymentMethod = this.value;
                        const instructions = document.getElementById('payment-instructions');
                        const allMethodInstructions = document.querySelectorAll('.payment-method-instructions');

                        if (paymentMethod) {
                                    instructions.classList.remove('d-none');
                                    allMethodInstructions.forEach(inst => {
                                                inst.classList.add('d-none');
                                    });
                                    document.getElementById(`${paymentMethod}-instructions`).classList.remove('d-none');
                        } else {
                                    instructions.classList.add('d-none');
                        }
            });

            document.querySelector('select[name="shipping_method"]').addEventListener('change', function() {
                        const form = document.getElementById('checkout-form');
                        const formData = new FormData(form);
                        console.log('Shipping method changed to:', this.value);
            });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>