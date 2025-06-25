<?php
global $conn, $product_id;

$stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
            echo '<div class="alert alert-danger">Produk tidak ditemukan.</div>';
            return;
}

$sold_stmt = mysqli_prepare($conn, "SELECT SUM(oi.quantity) as total_sold FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.product_id = ? AND o.status = 'Selesai'");
mysqli_stmt_bind_param($sold_stmt, "i", $product_id);
mysqli_stmt_execute($sold_stmt);
$sold_result = mysqli_stmt_get_result($sold_stmt);
$sold_data = mysqli_fetch_assoc($sold_result);
$total_terjual = $sold_data['total_sold'] ?? 0;
?>

<div class="row">
            <div class="col-md-6">
                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="col-md-6">
                        <h2 class="mt-3"><?= htmlspecialchars($product['name']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($product['description']) ?></p>
                        <h3 class="text-danger my-3">Rp <?= number_format($product['price'], 0, ',', '.') ?></h3>
                        <p>
                                    Status Stok:
                                    <?php if ($product['stock'] > 10): ?>
                                                <span class="badge bg-success">Tersedia</span>
                                    <?php elseif ($product['stock'] > 0): ?>
                                                <span class="badge bg-warning">Stok Terbatas</span>
                                    <?php else: ?>
                                                <span class="badge bg-danger">Habis</span>
                                    <?php endif; ?>
                                    (Sisa: <?= $product['stock'] ?>)
                        </p>
                        <?php if ($total_terjual > 0): ?>
                                    <p class="text-success fw-bold"><i class="fas fa-check-circle"></i> Terjual <?= $total_terjual ?></p>
                        <?php endif; ?>
                        <hr>

                        <form action="<?= BASE_URL ?>app/cart_action.php" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                    <div class="input-group mb-3" style="max-width: 200px;">
                                                <label class="input-group-text" for="quantity">Jumlah</label>
                                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                                    </div>

                                    <div class="mb-3">
                                                <label for="custom_text" class="form-label">Catatan untuk Penjual (Opsional)</label>
                                                <textarea class="form-control" name="customization_details" id="custom_text" rows="2"></textarea>
                                    </div>

                                    <?php if (isset($_SESSION['user_id'])): ?>
                                                <button type="submit" class="btn btn-primary btn-lg" <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                                                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                                </button>
                                    <?php else: ?>
                                                <a href="<?= BASE_URL ?>login" class="btn btn-primary btn-lg <?= $product['stock'] < 1 ? 'disabled' : '' ?>">
                                                            <i class="fas fa-sign-in-alt"></i> Login untuk Membeli
                                                </a>
                                    <?php endif; ?>
                        </form>

                        <div class="mt-5">
                                    <h4>Ulasan Pelanggan</h4>
                                    <hr>
                                    <p>Belum ada ulasan untuk produk ini.</p>
                        </div>
            </div>
</div>