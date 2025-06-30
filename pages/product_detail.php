<?php
global $conn, $product_id;

$stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
            echo '<div class="alert alert-danger mt-3 text-center">Produk tidak ditemukan.</div>';
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

$rating_filter = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$reviews_per_page = 10;
$offset = ($current_page - 1) * $reviews_per_page;

$count_query = "SELECT COUNT(id) as total_reviews FROM reviews WHERE product_id = ? AND is_approved = 1";
if ($rating_filter > 0 && $rating_filter <= 5) {
            $count_query .= " AND rating = ?";
}
$count_stmt = mysqli_prepare($conn, $count_query);
if ($rating_filter > 0 && $rating_filter <= 5) {
            mysqli_stmt_bind_param($count_stmt, "ii", $product_id, $rating_filter);
} else {
            mysqli_stmt_bind_param($count_stmt, "i", $product_id);
}
mysqli_stmt_execute($count_stmt);
$total_reviews = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total_reviews'];
$total_pages = ceil($total_reviews / $reviews_per_page);

$review_query = "SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? AND r.is_approved = 1";
if ($rating_filter > 0 && $rating_filter <= 5) {
            $review_query .= " AND r.rating = ?";
}
$review_query .= " ORDER BY r.created_at DESC LIMIT ?, ?";
$reviews_stmt = mysqli_prepare($conn, $review_query);

if ($rating_filter > 0 && $rating_filter <= 5) {
            mysqli_stmt_bind_param($reviews_stmt, "iiii", $product_id, $rating_filter, $offset, $reviews_per_page);
} else {
            mysqli_stmt_bind_param($reviews_stmt, "iii", $product_id, $offset, $reviews_per_page);
}
mysqli_stmt_execute($reviews_stmt);
$reviews_result = mysqli_stmt_get_result($reviews_stmt);
$reviews = mysqli_fetch_all($reviews_result, MYSQLI_ASSOC);

$rating_summary_stmt = mysqli_prepare($conn, "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM reviews WHERE product_id = ? AND is_approved = 1");
mysqli_stmt_bind_param($rating_summary_stmt, "i", $product_id);
mysqli_stmt_execute($rating_summary_stmt);
$rating_summary_result = mysqli_stmt_get_result($rating_summary_stmt);
$rating_summary = mysqli_fetch_assoc($rating_summary_result);
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
                                                            <div class="mb-3">
                                                                        <?php if ($rating_summary['total_reviews'] > 0): $avg_rating = round($rating_summary['avg_rating']); ?>
                                                                                    <span class="me-2">
                                                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                                                            <i class="fas fa-star <?= $i <= $avg_rating ? 'text-warning' : 'text-secondary' ?>"></i>
                                                                                                <?php endfor; ?>
                                                                                    </span>
                                                                                    <a href="#reviews-pane" class="text-muted text-decoration-none">(<?= $rating_summary['total_reviews'] ?> ulasan)</a>
                                                                        <?php else: ?>
                                                                                    <span class="text-muted">Belum ada ulasan</span>
                                                                        <?php endif; ?>
                                                            </div>
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
                                                                                    <p class="mb-1 text-success"><strong>Terjual: </strong> <?= $total_terjual ?></p>
                                                                        <?php endif; ?>
                                                            </div>
                                                            <form action="<?= BASE_URL ?>app/cart_action.php" method="POST" class="mt-4">
                                                                        <input type="hidden" name="action" value="add">
                                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                                        <div class="d-flex align-items-center mb-3">
                                                                                    <div class="input-group" style="width: 150px;">
                                                                                                <label class="input-group-text" for="quantity">Jumlah</label>
                                                                                                <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>">
                                                                                    </div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="custom_text" class="form-label">Catatan untuk Produk (Opsional)</label>
                                                                                    <textarea class="form-control" name="customization_details" id="custom_text" rows="2" placeholder="Contoh: Tolong bungkus dengan rapi."></textarea>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                                    <?php if (isset($_SESSION['user_id'])): ?>
                                                                                                <button type="submit" class="btn btn-primary btn-lg" <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                                                                                                            <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                                                                                                </button>
                                                                                    <?php else: ?>
                                                                                                <a href="<?= BASE_URL ?>login" class="btn btn-primary btn-lg <?= $product['stock'] < 1 ? 'disabled' : '' ?>">
                                                                                                            <i class="fas fa-sign-in-alt me-2"></i> Login untuk Membeli
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
                                                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description-pane" type="button">Deskripsi</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews-pane" type="button">Ulasan (<?= $rating_summary['total_reviews'] ?>)</button>
                                                </li>
                                    </ul>
                                    <div class="tab-content pt-4" id="myTabContent">
                                                <div class="tab-pane fade show active" id="description-pane" role="tabpanel">
                                                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                                                </div>
                                                <div class="tab-pane fade" id="reviews-pane" role="tabpanel">
                                                            <?php if ($rating_summary['total_reviews'] > 0): ?>
                                                                        <h4 class="mb-4">Ulasan Pelanggan</h4>
                                                                        <div class="rating-filter mb-4">
                                                                                    <div class="btn-group" role="group">
                                                                                                <a href="<?= BASE_URL ?>produk/detail/<?= $product_id ?>" class="btn btn-outline-secondary <?= $rating_filter == 0 ? 'active' : '' ?>">Semua</a>
                                                                                                <?php for ($star = 5; $star >= 1; $star--): ?>
                                                                                                            <a href="<?= BASE_URL ?>produk/detail/<?= $product_id ?>?rating=<?= $star ?>" class="btn btn-outline-secondary <?= $rating_filter == $star ? 'active' : '' ?>">
                                                                                                                        <i class="fas fa-star text-warning"></i> <?= $star ?>
                                                                                                            </a>
                                                                                                <?php endfor; ?>
                                                                                    </div>
                                                                        </div>

                                                                        <?php foreach ($reviews as $review): ?>
                                                                                    <div class="d-flex mb-4">
                                                                                                <div class="flex-shrink-0 me-3">
                                                                                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                                                                                        <?= strtoupper(substr($review['user_name'], 0, 1)) ?>
                                                                                                            </div>
                                                                                                </div>
                                                                                                <div class="flex-grow-1">
                                                                                                            <h5 class="mt-0 mb-1 text-dark"><?= htmlspecialchars($review['user_name']) ?></h5>
                                                                                                            <div>
                                                                                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                                                                                    <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-secondary' ?>" style="font-size: 0.9rem;"></i>
                                                                                                                        <?php endfor; ?>
                                                                                                            </div>
                                                                                                            <small class="text-muted">Diulas pada <?= date('d-m-Y | H:i', strtotime($review['created_at'])) ?></small>
                                                                                                            <p class="mt-1 mb-2"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                                                                                </div>
                                                                                    </div>
                                                                        <?php endforeach; ?>

                                                                        <?php if ($total_pages > 1): ?>
                                                                                    <nav aria-label="Reviews pagination">
                                                                                                <ul class="pagination justify-content-center">
                                                                                                            <?php if ($current_page > 1): ?>
                                                                                                                        <li class="page-item">
                                                                                                                                    <a class="page-link" href="?url=produk/detail/<?= $product_id ?>&rating=<?= $rating_filter ?>&page=<?= $current_page - 1 ?>">&laquo;</a>
                                                                                                                        </li>
                                                                                                            <?php endif; ?>

                                                                                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                                                                                        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>"><a class="page-link" href="?url=produk/detail/<?= $product_id ?>&rating=<?= $rating_filter ?>&page=<?= $i ?>"><?= $i ?></a></li>
                                                                                                            <?php endfor; ?>

                                                                                                            <?php if ($current_page < $total_pages): ?>
                                                                                                                        <li class="page-item">
                                                                                                                                    <a class="page-link" href="?url=produk/detail/<?= $product_id ?>&rating=<?= $rating_filter ?>&page=<?= $current_page + 1 ?>">&raquo;</a>
                                                                                                                        </li>
                                                                                                            <?php endif; ?>
                                                                                                </ul>
                                                                                    </nav>
                                                                        <?php endif; ?>
                                                            <?php else: ?>
                                                                        <div class="text-center p-4 bg-light rounded">
                                                                                    <p class="mb-0">Jadilah yang pertama memberi ulasan untuk produk ini!</p>
                                                                        </div>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>

                        <?php if (mysqli_num_rows($related_products_result) > 0): ?>
                                    <div class="related-products mt-5 pt-5 border-top">
                                                <div class="section-title text-center">
                                                            <h2>Anda Mungkin Juga Suka</h2>
                                                </div>
                                                <div class="row g-4">
                                                            <?php mysqli_data_seek($related_products_result, 0);
                                                            while ($related_product = mysqli_fetch_assoc($related_products_result)): $product = $related_product; ?>
                                                                        <?php include 'parts/product_card.php'; ?>
                                                            <?php endwhile; ?>
                                                </div>
                                    </div>
                        <?php endif; ?>
            </div>
</main>