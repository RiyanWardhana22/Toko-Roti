<h3 class="mb-4">Keranjang Belanja Anda</h3>
<?php if (!empty($_SESSION['cart'])): ?>
            <table class="table table-bordered">
                        <thead>
                                    <tr>
                                                <th>Produk</th>
                                                <th>Harga</th>
                                                <th style="width: 120px;">Jumlah</th>
                                                <th>Subtotal</th>
                                                <th>Aksi</th>
                                    </tr>
                        </thead>
                        <tbody>
                                    <?php
                                    $total_harga = 0;
                                    foreach ($_SESSION['cart'] as $key => $item):
                                                $subtotal = $item['price'] * $item['quantity'];
                                                $total_harga += $subtotal;
                                    ?>
                                                <tr>
                                                            <td>
                                                                        <?= htmlspecialchars($item['name']) ?>
                                                                        <?php if (!empty($item['customization'])): ?>
                                                                                    <br><small class="text-muted"><i>"<?= htmlspecialchars($item['customization']) ?>"</i></small>
                                                                        <?php endif; ?>
                                                            </td>
                                                            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                            <td>
                                                                        <form action="<?= BASE_URL ?>app/cart_action.php" method="POST" class="d-flex">
                                                                                    <input type="hidden" name="action" value="update">
                                                                                    <input type="hidden" name="cart_key" value="<?= $key ?>">
                                                                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control form-control-sm" min="1" onchange="this.form.submit()">
                                                                        </form>
                                                            </td>
                                                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                                            <td>
                                                                        <form action="<?= BASE_URL ?>app/cart_action.php" method="POST">
                                                                                    <input type="hidden" name="action" value="remove">
                                                                                    <input type="hidden" name="cart_key" value="<?= $key ?>">
                                                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                                        </form>
                                                            </td>
                                                </tr>
                                    <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                                    <tr>
                                                <th colspan="3" class="text-end">Total</th>
                                                <th colspan="2">Rp <?= number_format($total_harga, 0, ',', '.') ?></th>
                                    </tr>
                        </tfoot>
            </table>
            <div class="text-end">
                        <a href="<?= BASE_URL ?>checkout" class="btn btn-primary btn-lg">Lanjut ke Checkout</a>
            </div>
<?php else: ?>
            <div class="alert alert-info text-center">
                        Keranjang belanja Anda masih kosong. <a href="<?= BASE_URL ?>produk">Mulai belanja sekarang!</a>
            </div>
<?php endif; ?>