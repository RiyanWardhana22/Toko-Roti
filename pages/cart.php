<main>
            <div class="container py-5">
                        <?php if (!empty($_SESSION['cart'])): ?>
                                    <div class="row g-5">
                                                <div class="col-lg-8">
                                                            <?php
                                                            $total_harga = 0;
                                                            foreach ($_SESSION['cart'] as $key => $item):
                                                                        $subtotal = $item['price'] * $item['quantity'];
                                                                        $total_harga += $subtotal;
                                                            ?>
                                                                        <div class="cart-item">
                                                                                    <div class="row align-items-center">
                                                                                                <div class="col-md-2 col-3 cart-item-img">
                                                                                                            <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                                                                                </div>
                                                                                                <div class="col-md-5 col-9 cart-item-details">
                                                                                                            <h5><?= htmlspecialchars($item['name']) ?></h5>
                                                                                                            <?php if (!empty($item['customization'])): ?>
                                                                                                                        <p class="text-muted mb-1"><em>"<?= htmlspecialchars($item['customization']) ?>"</em></p>
                                                                                                            <?php endif; ?>
                                                                                                            <p class="text-muted mb-0">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                                                                                                </div>
                                                                                                <div class="col-md-5 col-12 mt-3 mt-md-0 d-flex justify-content-between align-items-center cart-item-actions">
                                                                                                            <div class="d-flex align-items-center">
                                                                                                                        <form action="<?= BASE_URL ?>app/cart_action.php" method="POST" class="me-3">
                                                                                                                                    <input type="hidden" name="action" value="update">
                                                                                                                                    <input type="hidden" name="cart_key" value="<?= $key ?>">
                                                                                                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm" min="1" onchange="this.form.submit()">
                                                                                                                        </form>
                                                                                                                        <form action="<?= BASE_URL ?>app/cart_action.php" method="POST">
                                                                                                                                    <input type="hidden" name="action" value="remove">
                                                                                                                                    <input type="hidden" name="cart_key" value="<?= $key ?>">
                                                                                                                                    <button type="submit" class="btn-remove"><i class="fa-solid fa-trash-can"></i></button>
                                                                                                                        </form>
                                                                                                            </div>
                                                                                                            <strong class="fs-5 me-4">Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                                                                                                </div>
                                                                                    </div>
                                                                        </div>
                                                            <?php endforeach; ?>
                                                </div>

                                                <div class="col-lg-4">
                                                            <div class="cart-summary-card">
                                                                        <h4>Ringkasan Belanja</h4>
                                                                        <hr>
                                                                        <div class="d-flex justify-content-between mb-3">
                                                                                    <span>Subtotal</span>
                                                                                    <strong class="me-2">Rp <?= number_format($total_harga, 0, ',', '.') ?></strong>
                                                                        </div>
                                                                        <p class="text-muted small">Ongkos kirim dan diskon akan dihitung di halaman checkout.</p>
                                                                        <div class="d-grid">
                                                                                    <a href="<?= BASE_URL ?>checkout" class="btn btn-primary btn-lg">Lanjut ke Checkout</a>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        <?php else: ?>
                                    <div class="text-center py-5">
                                                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                                                <h3>Keranjang Anda Kosong</h3>
                                                <p class="text-muted">Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
                                                <a href="<?= BASE_URL ?>produk" class="btn btn-primary mt-3">Mulai Belanja</a>
                                    </div>
                        <?php endif; ?>
            </div>
</main>