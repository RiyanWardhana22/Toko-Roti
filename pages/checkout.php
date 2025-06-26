<?php
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

<h2>Checkout</h2>
<form action="<?= BASE_URL ?>app/order_action.php" method="POST">
            <div class="row">
                        <div class="col-md-6">
                                    <h4>Data Pelanggan</h4>
                                    <div class="mb-3"><label>Nama</label><input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></div>
                                    <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
                                    <div class="mb-3"><label>Nomor Telepon</label><input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required></div>
                                    <div class="mb-3"><label>Alamat Pengiriman</label><textarea class="form-control" name="address" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea></div>
                                    <hr>
                                    <h4>Opsi Pengiriman</h4>
                                    <div class="form-check"><input class="form-check-input shipping-option" type="radio" name="shipping_method" id="gosend" value="Gosend Same Day" data-cost="15000" required><label class="form-check-label" for="gosend">Gosend Same Day - Rp 15.000</label></div>
                                    <div class="form-check"><input class="form-check-input shipping-option" type="radio" name="shipping_method" id="grab" value="Grab Express" data-cost="18000"><label class="form-check-label" for="grab">Grab Express - Rp 18.000</label></div>
                                    <div class="form-check"><input class="form-check-input shipping-option" type="radio" name="shipping_method" id="ambil" value="Ambil di Toko" data-cost="0"><label class="form-check-label" for="ambil">Ambil di Toko - Rp 0</label></div>
                                    <hr>
                                    <h4>Opsi Pembayaran</h4>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" id="transfer" value="Transfer Bank Manual" checked required><label class="form-check-label" for="transfer">Transfer Bank Manual</label></div>
                        </div>

                        <div class="col-md-6">
                                    <div class="card">
                                                <div class="card-header">
                                                            <h4>Ringkasan Pesanan</h4>
                                                </div>
                                                <div class="card-body">
                                                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                                                        <div class="d-flex justify-content-between">
                                                                                    <p><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</p>
                                                                                    <p>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                                                                        </div>
                                                            <?php endforeach; ?>
                                                            <hr>
                                                            <div class="d-flex justify-content-between">
                                                                        <p>Subtotal</p>
                                                                        <p>Rp <?= number_format($subtotal, 0, ',', '.') ?></p>
                                                            </div>

                                                            <div class="d-flex justify-content-between">
                                                                        <p>Ongkos Kirim</p>
                                                                        <p id="shipping-cost-text">Rp 0</p>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-success" id="discount-row" style="display: none;">
                                                                        <p>Diskon (<span id="voucher-code-text"></span>)</p>
                                                                        <p id="discount-text">- Rp 0</p>
                                                            </div>
                                                            <hr>
                                                            <div id="voucher-area">
                                                                        <label for="voucher_code" class="form-label">Punya Kode Voucher?</label>
                                                                        <div class="input-group">
                                                                                    <input type="text" class="form-control" id="voucher_code" placeholder="Masukkan kode voucher">
                                                                                    <button class="btn btn-outline-primary" type="button" id="apply-voucher-btn">Terapkan</button>
                                                                        </div>
                                                                        <div id="voucher-message" class="mt-2"></div>
                                                            </div>
                                                            <hr>
                                                            <div class="d-flex justify-content-between fw-bold fs-5">
                                                                        <p>Total Akhir</p>
                                                                        <p id="total-cost-text">Rp <?= number_format($subtotal, 0, ',', '.') ?></p>
                                                            </div>
                                                </div>
                                                <div class="card-footer">
                                                            <input type="hidden" id="shipping_cost" name="shipping_cost" value="0">
                                                            <input type="hidden" id="total_amount" name="total_amount" value="<?= $subtotal ?>">
                                                            <button type="submit" class="btn btn-primary w-100 btn-lg">Buat Pesanan</button>
                                                </div>
                                    </div>
                        </div>
            </div>

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

                                                                                    discountText.innerText = '- Rp ' + discountAmount.toLocaleString('id-ID');
                                                                                    voucherCodeText.innerText = voucherCode.toUpperCase();
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
</form>