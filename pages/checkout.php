<?php
// ... (Bagian PHP di atas tidak ada perubahan) ...
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}
if (empty($_SESSION['cart'])) {
            header('Location: ' . BASE_URL . 'cart');
            exit();
}
unset($_SESSION['voucher']);
$user_id = $_SESSION['user_id'];
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_result);
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
}
?>

<main>
            <div class="container py-5">

                        <form action="<?= BASE_URL ?>app/order_action.php" method="POST">
                                    <div class="row g-5">
                                                <div class="col-lg-7">
                                                            <div class="card checkout-card">
                                                                        <div class="card-body p-4">
                                                                                    <h5 class="card-title mb-4">Data Pelanggan & Alamat Pengiriman</h5>
                                                                                    <div class="row">
                                                                                                <div class="col-md-6 mb-3"><label class="form-label">Nama</label><input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
                                                                                                <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
                                                                                    </div>
                                                                                    <div class="mb-3"><label class="form-label">Nomor Telepon</label><input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required></div>
                                                                                    <div class="mb-3"><label class="form-label">Alamat Pengiriman Lengkap</label><textarea class="form-control" name="address" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea></div>
                                                                        </div>
                                                            </div>
                                                            <div class="card checkout-card">
                                                                        <div class="card-body p-4">
                                                                                    <h5 class="card-title mb-4">Opsi Pembayaran</h5>
                                                                                    <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" id="transfer" value="Transfer Bank Manual" checked required><label class="form-check-label" for="transfer">Transfer Bank Manual</label></div>
                                                                        </div>
                                                            </div>
                                                            <div class="card checkout-card">
                                                                        <div class="card-body p-4">
                                                                                    <h5 class="card-title mb-4">Opsi Pengiriman</h5>
                                                                                    <div class="form-check"><input class="form-check-input shipping-option" type="radio" name="shipping_method" id="gosend" value="Gosend Same Day" data-cost="15000" required><label class="form-check-label" for="gosend">Gosend Same Day - Rp 15.000</label></div>
                                                                                    <div class="form-check"><input class="form-check-input shipping-option" type="radio" name="shipping_method" id="grab" value="Grab Express" data-cost="18000"><label class="form-check-label" for="grab">Grab Express - Rp 18.000</label></div>
                                                                                    <div class="form-check"><input class="form-check-input shipping-option" type="radio" name="shipping_method" id="ambil" value="Ambil di Toko" data-cost="0"><label class="form-check-label" for="ambil">Ambil di Toko - Rp 0</label></div>
                                                                        </div>
                                                            </div>
                                                </div>

                                                <div class="col-lg-5">
                                                            <div class="card checkout-card">
                                                                        <div class="card-body p-4">
                                                                                    <h5 class="card-title mb-4">Ringkasan Pesanan</h5>
                                                                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                                                                                <div class="d-flex justify-content-between small mb-2">
                                                                                                            <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                                                                                                            <span>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                                                                                                </div>
                                                                                    <?php endforeach; ?>
                                                                                    <hr>
                                                                                    <ul class="list-group list-group-flush">
                                                                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Subtotal</span><span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span></li>
                                                                                                <li class="list-group-item d-flex justify-content-between px-0"><span>Ongkos Kirim</span><span id="shipping-cost-text">Rp 0</span></li>
                                                                                                <li class="list-group-item d-flex justify-content-between px-0 text-success" id="discount-row" style="display: none;">
                                                                                                            <span id="discount-label">Diskon</span><span id="discount-text">- Rp 0</span>
                                                                                                </li>
                                                                                                <div id="voucher-area" class="my-3">
                                                                                                            <label for="voucher_code" class="form-label">Punya Kode Voucher?</label>
                                                                                                            <div class="input-group">
                                                                                                                        <input type="text" class="form-control" id="voucher_code" placeholder="Masukkan kode">
                                                                                                                        <button class="btn btn-outline-primary" type="button" id="apply-voucher-btn">Terapkan</button>
                                                                                                            </div>
                                                                                                            <div id="voucher-message" class="mt-2"></div>
                                                                                                </div>
                                                                                                <li class="list-group-item d-flex justify-content-between px-0 bg-light">
                                                                                                            <strong class="fs-5">Total Akhir</strong>
                                                                                                            <strong class="fs-5" id="total-cost-text">Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                                                                                                </li>
                                                                                    </ul>
                                                                        </div>
                                                            </div>
                                                            <div class="d-grid">
                                                                        <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">
                                                                        <input type="hidden" id="total_amount" name="total_amount" value="<?= $subtotal ?>">
                                                                        <button type="submit" class="btn btn-primary btn-lg">Buat Pesanan & Bayar</button>
                                                            </div>
                                                </div>
                                    </div>
                        </form>
            </div>
</main>

<script>
            document.addEventListener('DOMContentLoaded', function() {
                        const subtotal = <?= $subtotal ?>;
                        let shippingCost = 0;
                        let discountAmount = 0;

                        const shippingOptions = document.querySelectorAll('.shipping-option');
                        const applyBtn = document.getElementById('apply-voucher-btn');
                        const voucherCodeInput = document.getElementById('voucher_code');
                        const voucherMessageDiv = document.getElementById('voucher-message');
                        const shippingCostText = document.getElementById('shipping-cost-text');
                        const discountRow = document.getElementById('discount-row');
                        const discountLabel = document.getElementById('discount-label');
                        const discountText = document.getElementById('discount-text');
                        const voucherCodeText = document.getElementById('voucher-code-text');
                        const totalCostText = document.getElementById('total-cost-text');
                        const shippingCostInput = document.getElementById('shipping_cost');
                        const totalAmountInput = document.getElementById('total_amount');

                        function updateTotal() {
                                    const newTotal = subtotal + shippingCost - discountAmount;
                                    totalCostText.innerText = 'Rp ' + newTotal.toLocaleString('id-ID');
                                    totalAmountInput.value = newTotal;
                        }

                        shippingOptions.forEach(option => {
                                    option.addEventListener('change', function() {
                                                shippingCost = parseInt(this.dataset.cost);
                                                shippingCostText.innerText = 'Rp ' + shippingCost.toLocaleString('id-ID');
                                                shippingCostInput.value = shippingCost;
                                                updateTotal();
                                    });
                        });

                        applyBtn.addEventListener('click', function() {
                                    const voucherCode = voucherCodeInput.value.trim();
                                    if (voucherCode === '') {
                                                voucherMessageDiv.innerHTML = '<div class="text-danger small">Silakan masukkan kode voucher.</div>';
                                                return;
                                    }

                                    this.disabled = true;
                                    voucherMessageDiv.innerHTML = '<div class="text-muted small">Menerapkan...</div>';

                                    fetch('<?= BASE_URL ?>app/voucher_action.php', {
                                                            method: 'POST',
                                                            headers: {
                                                                        'Content-Type': 'application/x-www-form-urlencoded',
                                                            },
                                                            body: new URLSearchParams({
                                                                        'action': 'apply_voucher',
                                                                        'voucher_code': voucherCode,
                                                                        'subtotal': subtotal
                                                            })
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                            if (data.status === 'success') {
                                                                        voucherMessageDiv.innerHTML = `<div class="text-success small">${data.message}</div>`;
                                                                        discountAmount = parseFloat(data.discount_amount);
                                                                        if (data.voucher_type === 'percentage') {
                                                                                    discountLabel.innerText = `Diskon (${data.voucher_value}%)`;
                                                                        } else {
                                                                                    discountLabel.innerText = 'Diskon';
                                                                        }

                                                                        discountText.innerText = '- Rp ' + discountAmount.toLocaleString('id-ID');
                                                                        discountRow.style.display = 'flex';
                                                            } else {
                                                                        voucherMessageDiv.innerHTML = `<div class="text-danger small">${data.message}</div>`;
                                                                        discountAmount = 0;
                                                                        discountRow.style.display = 'none';
                                                            }
                                                            updateTotal();
                                                            this.disabled = false;
                                                })
                                                .catch(error => {
                                                            voucherMessageDiv.innerHTML = '<div class="text-danger small">Terjadi kesalahan. Silakan coba lagi.</div>';
                                                            this.disabled = false;
                                                });
                        });
            });
</script>