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

$related_products_stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
mysqli_stmt_bind_param($related_products_stmt, "ii", $product['category_id'], $product_id);
mysqli_stmt_execute($related_products_stmt);
$related_products_result = mysqli_stmt_get_result($related_products_stmt);
?>

<main>
            <div class="container py-5">
                        <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Beranda</a></li>
                                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>produk">Produk</a></li>
                                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>produk?kategori=<?= $product['category_slug'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                                                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
                                    </ol>
                        </nav>

                        <div class="product-detail-container">
                                    <div class="row g-5">
                                                <div class="col-lg-6 product-gallery">
                                                            <div class="main-image mb-3">
                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
                                                            </div>
                                                </div>

                                                <div class="col-lg-6 product-info">
                                                            <h2><?= htmlspecialchars($product['name']) ?></h2>
                                                            <div class="price my-3">
                                                                        <span>Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                            </div>
                                                            <div class="product-meta mb-3">
                                                                        <p class="mb-1"><strong>Kategori:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                                                                        <p class="mb-1"><strong>Stok:</strong>
                                                                                    <?php if ($product['stock'] > 10): ?>
                                                                                                <span class="badge bg-success">Tersedia</span> (<?= $product['stock'] ?>)
                                                                                    <?php elseif ($product['stock'] > 0): ?>
                                                                                                <span class="badge bg-warning">Stok Terbatas</span> (<?= $product['stock'] ?>)
                                                                                    <?php else: ?>
                                                                                                <span class="badge bg-danger">Habis</span>
                                                                                    <?php endif; ?>
                                                                        </p>
                                                                        <?php if ($total_terjual > 0): ?>
                                                                                    <p class="mb-1 text-success"><strong>Terjual:</strong> <?= $total_terjual ?> buah</p>
                                                                        <?php endif; ?>
                                                            </div>

                                                            <form action="<?= BASE_URL ?>app/cart_action.php" method="POST" class="mt-4">
                                                                        <input type="hidden" name="action" value="add">
                                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                                                        <div class="input-group mb-3" style="width: 150px;">
                                                                                    <label class="input-group-text" for="quantity">Jumlah</label>
                                                                                    <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="custom_text" class="form-label">Catatan untuk Produk (Opsional)</label>
                                                                                    <textarea class="form-control" name="customization_details" id="custom_text" rows="2" placeholder="Contoh: Tolong bungkus dengan rapi."></textarea>
                                                                        </div>

                                                                        <div class="d-flex align-items-center">
                                                                                    <?php if (isset($_SESSION['user_id'])): ?>
                                                                                                <button type="submit" class="btn btn-primary btn-lg flex-grow-1" <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                                                                                                            <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                                                                                                </button>
                                                                                    <?php else: ?>
                                                                                                <a href="<?= BASE_URL ?>login" class="btn bg-primary btn-primary flex-grow-1 <?= $product['stock'] < 1 ? 'disabled' : '' ?>">
                                                                                                            <i class="fas fa-sign-in-alt me-2"></i> Login
                                                                                                </a>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                            </form>
                                                </div>
                                    </div>
                        </div>

                        <div class="product-tabs mt-5">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-pane" type="button">Deskripsi</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-pane" type="button">Ulasan</button>
                                                </li>
                                    </ul>
                                    <div class="tab-content pt-4" id="myTabContent">
                                                <div class="tab-pane fade show active" id="description-pane" role="tabpanel">
                                                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                                                </div>
                                                <div class="tab-pane fade" id="reviews-pane" role="tabpanel">
                                                            Belum ada ulasan untuk produk ini.
                                                </div>
                                    </div>
                        </div>

                        <?php if (mysqli_num_rows($related_products_result) > 0): ?>
                                    <div class="related-products mt-5 pt-5 border-top">
                                                <div class="section-title text-center">
                                                            <h2>Anda Mungkin Juga Suka</h2>
                                                </div>
                                                <div class="row g-4">
                                                            <?php while ($related_product = mysqli_fetch_assoc($related_products_result)):
                                                                        $product = $related_product;
                                                            ?>
                                                                        <?php include 'parts/product_card.php'; ?>
                                                            <?php endwhile; ?>
                                                </div>
                                    </div>
                        <?php endif; ?>
            </div>
</main>