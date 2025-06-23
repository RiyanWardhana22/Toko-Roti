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
?>

<div class="row">
            <div class="col-md-6">
                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="col-md-6">
                        <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>produk">Produk</a></li>
                                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>produk?kategori=<?= $product['category_slug'] ?? '' ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                                                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
                                    </ol>
                        </nav>

                        <h2><?= htmlspecialchars($product['name']) ?></h2>
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
                                    (<?= $product['stock'] ?>)
                        </p>

                        <hr>

                        <?php if (isset($_SESSION['user_id'])): ?>
                                    <form action="<?= BASE_URL ?>app/cart_action.php" method="POST">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                                <div class="input-group mb-3" style="max-width: 200px;">
                                                            <label class="input-group-text" for="quantity">Jumlah</label>
                                                            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                                                </div>

                                                <button type="submit" class="btn btn-primary btn-lg" <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                                                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                                </button>
                                    </form>
                        <?php else: ?>
                                    <div class="input-group mb-3" style="max-width: 200px;">
                                                <label class="input-group-text" for="quantity">Jumlah</label>
                                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                                    </div>
                                    <a href="<?= BASE_URL ?>login" class="btn btn-primary btn-lg <?= $product['stock'] < 1 ? 'disabled' : '' ?>">
                                                <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                    </a>
                        <?php endif; ?>

                        <div class="mt-5">
                                    <h4>Ulasan Pelanggan</h4>
                                    <hr>
                                    <p>Belum ada ulasan untuk produk ini.</p>
                        </div>
            </div>
</div>