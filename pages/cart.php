<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('/pages/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$cart_items = getCartItems($_SESSION['user_id']);
$subtotal = calculateCartSubtotal($cart_items);
?>

<!-- Shopping Cart -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-5">Keranjang Belanja</h2>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">
                Keranjang belanja Anda kosong. <a href="/pages/products.php">Mulai belanja</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="/assets/uploads/<?= $item['image'] ?>"
                                                            class="img-thumbnail me-3"
                                                            width="80"
                                                            alt="<?= $item['name'] ?>">
                                                        <div>
                                                            <h6 class="mb-0"><?= $item['name'] ?></h6>
                                                            <?php if ($item['variant_name']): ?>
                                                                <small class="text-muted">Varian: <?= $item['variant_name'] ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                                </td>
                                                <td>
                                                    <div class="input-group" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary update-qty"
                                                            type="button"
                                                            data-action="decrease"
                                                            data-cart-id="<?= $item['cart_id'] ?>">
                                                            -
                                                        </button>
                                                        <input type="text" class="form-control text-center qty-input"
                                                            value="<?= $item['quantity'] ?>"
                                                            data-cart-id="<?= $item['cart_id'] ?>"
                                                            data-max="<?= $item['stock'] ?>">
                                                        <button class="btn btn-outline-secondary update-qty"
                                                            type="button"
                                                            data-action="increase"
                                                            data-cart-id="<?= $item['cart_id'] ?>">
                                                            +
                                                        </button>
                                                    </div>
                                                    <?php if ($item['quantity'] > $item['stock']): ?>
                                                        <small class="text-danger">Stok tersedia: <?= $item['stock'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-danger remove-from-cart"
                                                        data-cart-id="<?= $item['cart_id'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <a href="/pages/products.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Lanjutkan Belanja
                        </a>
                        <button class="btn btn-outline-danger" id="clear-cart">
                            <i class="fas fa-trash me-2"></i> Kosongkan Keranjang
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Ringkasan Belanja</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ongkos Kirim:</span>
                                <span class="text-muted">Akan dihitung saat checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>

                            <div class="mt-4">
                                <a href="/pages/checkout.php" class="btn btn-primary w-100 py-2">
                                    Lanjut ke Checkout
                                </a>
                            </div>

                            <?php if (hasCoupons()): ?>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Kode Promo">
                                        <button class="btn btn-outline-secondary" type="button">Terapkan</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    // Update quantity
    document.querySelectorAll('.update-qty').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const cartId = this.getAttribute('data-cart-id');
            const input = document.querySelector(`.qty-input[data-cart-id="${cartId}"]`);
            let qty = parseInt(input.value);
            const max = parseInt(input.getAttribute('data-max'));

            if (action === 'increase' && qty < max) {
                qty++;
            } else if (action === 'decrease' && qty > 1) {
                qty--;
            }

            if (qty !== parseInt(input.value)) {
                updateCartItem(cartId, qty);
            }
        });
    });

    // Handle direct input change
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.getAttribute('data-cart-id');
            let qty = parseInt(this.value) || 1;
            const max = parseInt(this.getAttribute('data-max'));

            if (qty < 1) qty = 1;
            if (qty > max) qty = max;

            this.value = qty;

            if (qty !== parseInt(this.getAttribute('data-original-value'))) {
                updateCartItem(cartId, qty);
            }
        });
    });

    // Remove item from cart
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
                const cartId = this.getAttribute('data-cart-id');
                removeFromCart(cartId);
            }
        });
    });

    // Clear cart
    document.getElementById('clear-cart').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin mengosongkan keranjang belanja?')) {
            clearCart();
        }
    });

    // Function to update cart item
    function updateCartItem(cartId, quantity) {
        fetch('/includes/update-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
    }

    // Function to remove from cart
    function removeFromCart(cartId) {
        fetch('/includes/remove-from-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_id: cartId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    document.getElementById('cart-count').textContent = data.cart_count;
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
    }

    // Function to clear cart
    function clearCart() {
        fetch('/includes/clear-cart.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    document.getElementById('cart-count').textContent = 0;
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>