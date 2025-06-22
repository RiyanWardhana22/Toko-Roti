<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            redirect('/pages/products.php');
}

$product_id = (int)$_GET['id'];
$product = getProductById($product_id);

if (!$product) {
            redirect('/pages/products.php');
}

// Tambah view count
incrementProductViewCount($product_id);
?>

<!-- Product Detail -->
<section class="py-5">
            <div class="container">
                        <div class="row">
                                    <div class="col-md-6">
                                                <!-- Main Product Image -->
                                                <div class="mb-3">
                                                            <img id="main-product-image" src="/assets/uploads/<?= $product['image'] ?>"
                                                                        class="img-fluid rounded" alt="<?= $product['name'] ?>">
                                                </div>

                                                <!-- Thumbnail Gallery -->
                                                <div class="row g-2">
                                                            <div class="col-3">
                                                                        <img src="/assets/uploads/<?= $product['image'] ?>"
                                                                                    class="img-fluid thumbnail-img cursor-pointer"
                                                                                    onclick="changeMainImage(this)"
                                                                                    alt="<?= $product['name'] ?>">
                                                            </div>
                                                            <?php foreach ($product['images'] as $image): ?>
                                                                        <div class="col-3">
                                                                                    <img src="/assets/uploads/<?= $image['image'] ?>"
                                                                                                class="img-fluid thumbnail-img cursor-pointer"
                                                                                                onclick="changeMainImage(this)"
                                                                                                alt="<?= $product['name'] ?>">
                                                                        </div>
                                                            <?php endforeach; ?>
                                                </div>
                                    </div>

                                    <div class="col-md-6">
                                                <h2><?= $product['name'] ?></h2>
                                                <div class="mb-3">
                                                            <span class="text-muted">Kategori: </span>
                                                            <a href="/pages/products.php?category=<?= $product['category_id'] ?>" class="text-decoration-none">
                                                                        <?= $product['category_name'] ?>
                                                            </a>
                                                </div>

                                                <!-- Rating -->
                                                <div class="mb-3">
                                                            <div class="text-warning">
                                                                        <?= str_repeat('<i class="fas fa-star"></i>', floor($product['avg_rating'])) ?>
                                                                        <?= ($product['avg_rating'] - floor($product['avg_rating']) >= 0.5) ? '<i class="fas fa-star-half-alt"></i>' : '' ?>
                                                                        <?= str_repeat('<i class="far fa-star"></i>', 5 - ceil($product['avg_rating'])) ?>
                                                                        <span class="text-muted ms-2">(<?= $product['review_count'] ?> ulasan)</span>
                                                            </div>
                                                </div>

                                                <!-- Price -->
                                                <div class="mb-4">
                                                            <?php if ($product['discount_price']): ?>
                                                                        <h3 class="text-danger">Rp <?= number_format($product['discount_price'], 0, ',', '.') ?></h3>
                                                                        <h5 class="text-decoration-line-through text-muted">Rp <?= number_format($product['price'], 0, ',', '.') ?></h5>
                                                                        <span class="badge bg-danger"><?= calculateDiscountPercentage($product['price'], $product['discount_price']) ?>% OFF</span>
                                                            <?php else: ?>
                                                                        <h3>Rp <?= number_format($product['price'], 0, ',', '.') ?></h3>
                                                            <?php endif; ?>
                                                </div>

                                                <!-- Stock Status -->
                                                <div class="mb-4">
                                                            <?php if ($product['stock'] > 10): ?>
                                                                        <span class="badge bg-success">Tersedia</span>
                                                            <?php elseif ($product['stock'] > 0): ?>
                                                                        <span class="badge bg-warning text-dark">Stok Terbatas</span>
                                                            <?php else: ?>
                                                                        <span class="badge bg-danger">Habis</span>
                                                            <?php endif; ?>
                                                            <span class="text-muted ms-2"><?= $product['stock'] ?> tersedia</span>
                                                </div>

                                                <!-- Description -->
                                                <div class="mb-4">
                                                            <h5>Deskripsi Produk</h5>
                                                            <p><?= nl2br($product['description']) ?></p>
                                                </div>

                                                <!-- Add to Cart Form -->
                                                <form class="mb-5" id="add-to-cart-form">
                                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                                            <?php if ($product['has_variants']): ?>
                                                                        <!-- Variant Selection -->
                                                                        <div class="mb-3">
                                                                                    <label class="form-label">Varian</label>
                                                                                    <select class="form-select" name="variant_id" required>
                                                                                                <option value="">Pilih varian</option>
                                                                                                <?php foreach (getProductVariants($product['id']) as $variant): ?>
                                                                                                            <option value="<?= $variant['id'] ?>" data-price="<?= $variant['price'] ?>"
                                                                                                                        <?= $variant['stock'] <= 0 ? 'disabled' : '' ?>>
                                                                                                                        <?= $variant['name'] ?>
                                                                                                                        (Rp <?= number_format($variant['price'], 0, ',', '.') ?>)
                                                                                                                        <?= $variant['stock'] <= 0 ? '- Habis' : '' ?>
                                                                                                            </option>
                                                                                                <?php endforeach; ?>
                                                                                    </select>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <!-- Quantity -->
                                                            <div class="mb-3">
                                                                        <label class="form-label">Jumlah</label>
                                                                        <div class="input-group" style="width: 150px;">
                                                                                    <button class="btn btn-outline-secondary" type="button" id="decrement-qty">-</button>
                                                                                    <input type="number" class="form-control text-center" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                                                                                    <button class="btn btn-outline-secondary" type="button" id="increment-qty">+</button>
                                                                        </div>
                                                            </div>

                                                            <!-- Buttons -->
                                                            <div class="d-flex gap-2">
                                                                        <button type="submit" class="btn btn-primary flex-grow-1 py-2"
                                                                                    <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                                                                    <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                                                                        </button>
                                                                        <button type="button" class="btn btn-outline-secondary" id="add-to-wishlist"
                                                                                    data-product-id="<?= $product['id'] ?>">
                                                                                    <i class="far fa-heart"></i>
                                                                        </button>
                                                            </div>
                                                </form>

                                                <!-- Share Buttons -->
                                                <div class="border-top pt-3">
                                                            <span class="text-muted me-2">Bagikan:</span>
                                                            <a href="#" class="text-decoration-none me-2" onclick="shareProduct('facebook')">
                                                                        <i class="fab fa-facebook-f"></i>
                                                            </a>
                                                            <a href="#" class="text-decoration-none me-2" onclick="shareProduct('twitter')">
                                                                        <i class="fab fa-twitter"></i>
                                                            </a>
                                                            <a href="#" class="text-decoration-none me-2" onclick="shareProduct('whatsapp')">
                                                                        <i class="fab fa-whatsapp"></i>
                                                            </a>
                                                </div>
                                    </div>
                        </div>

                        <!-- Product Tabs -->
                        <div class="row mt-5">
                                    <div class="col-12">
                                                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                                                                    data-bs-target="#description" type="button" role="tab">
                                                                                    Deskripsi Lengkap
                                                                        </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                                                                    data-bs-target="#reviews" type="button" role="tab">
                                                                                    Ulasan (<?= $product['review_count'] ?>)
                                                                        </button>
                                                            </li>
                                                </ul>
                                                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="productTabsContent">
                                                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                                                        <h5>Detail Produk</h5>
                                                                        <?= nl2br($product['full_description']) ?>

                                                                        <h5 class="mt-4">Bahan-Bahan</h5>
                                                                        <p><?= nl2br($product['ingredients']) ?></p>

                                                                        <h5 class="mt-4">Informasi Gizi (per 100g)</h5>
                                                                        <div class="table-responsive">
                                                                                    <table class="table table-bordered">
                                                                                                <tbody>
                                                                                                            <tr>
                                                                                                                        <td>Energi</td>
                                                                                                                        <td><?= $product['nutrition_energy'] ?> kkal</td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                        <td>Protein</td>
                                                                                                                        <td><?= $product['nutrition_protein'] ?> g</td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                        <td>Karbohidrat</td>
                                                                                                                        <td><?= $product['nutrition_carbs'] ?> g</td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                        <td>Lemak</td>
                                                                                                                        <td><?= $product['nutrition_fat'] ?> g</td>
                                                                                                            </tr>
                                                                                                </tbody>
                                                                                    </table>
                                                                        </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                                                        <?php if ($product['review_count'] > 0): ?>
                                                                                    <div class="mb-4">
                                                                                                <h5>Ulasan Pelanggan</h5>
                                                                                                <?php foreach (getProductReviews($product['id']) as $review): ?>
                                                                                                            <div class="card mb-3">
                                                                                                                        <div class="card-body">
                                                                                                                                    <div class="d-flex justify-content-between">
                                                                                                                                                <div>
                                                                                                                                                            <h6 class="mb-1"><?= $review['user_name'] ?></h6>
                                                                                                                                                            <div class="text-warning mb-2">
                                                                                                                                                                        <?= str_repeat('<i class="fas fa-star"></i>', $review['rating']) ?>
                                                                                                                                                                        <?= str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']) ?>
                                                                                                                                                            </div>
                                                                                                                                                </div>
                                                                                                                                                <div class="text-muted small">
                                                                                                                                                            <?= date('d M Y', strtotime($review['created_at'])) ?>
                                                                                                                                                </div>
                                                                                                                                    </div>
                                                                                                                                    <p class="card-text"><?= nl2br($review['comment']) ?></p>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                <?php endforeach; ?>
                                                                                    </div>
                                                                        <?php else: ?>
                                                                                    <div class="alert alert-info">
                                                                                                Belum ada ulasan untuk produk ini.
                                                                                    </div>
                                                                        <?php endif; ?>

                                                                        <!-- Add Review Form -->
                                                                        <?php if (isLoggedIn() && hasPurchasedProduct($_SESSION['user_id'], $product['id'])): ?>
                                                                                    <div class="mt-4">
                                                                                                <h5>Tulis Ulasan</h5>
                                                                                                <form id="review-form">
                                                                                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                                                                            <div class="mb-3">
                                                                                                                        <label class="form-label">Rating</label>
                                                                                                                        <div class="rating-stars">
                                                                                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                                                                                                <i class="far fa-star" data-rating="<?= $i ?>"></i>
                                                                                                                                    <?php endfor; ?>
                                                                                                                                    <input type="hidden" name="rating" id="rating-value" required>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                            <div class="mb-3">
                                                                                                                        <label class="form-label">Ulasan Anda</label>
                                                                                                                        <textarea class="form-control" name="comment" rows="3" required></textarea>
                                                                                                            </div>
                                                                                                            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                                                                                                </form>
                                                                                    </div>
                                                                        <?php elseif (!isLoggedIn()): ?>
                                                                                    <div class="alert alert-warning">
                                                                                                Silakan <a href="/pages/auth/login.php">login</a> untuk memberikan ulasan.
                                                                                    </div>
                                                                        <?php elseif (!hasPurchasedProduct($_SESSION['user_id'], $product['id'])): ?>
                                                                                    <div class="alert alert-warning">
                                                                                                Hanya pelanggan yang telah membeli produk ini yang dapat memberikan ulasan.
                                                                                    </div>
                                                                        <?php endif; ?>
                                                            </div>
                                                </div>
                                    </div>
                        </div>

                        <!-- Related Products -->
                        <div class="row mt-5">
                                    <div class="col-12">
                                                <h3 class="mb-4">Produk Terkait</h3>
                                                <div class="row">
                                                            <?php foreach (getRelatedProducts($product['id'], $product['category_id'], 4) as $related): ?>
                                                                        <div class="col-md-3 mb-4">
                                                                                    <div class="card h-100">
                                                                                                <img src="/assets/uploads/<?= $related['image'] ?>" class="card-img-top" alt="<?= $related['name'] ?>">
                                                                                                <div class="card-body">
                                                                                                            <h5 class="card-title"><?= $related['name'] ?></h5>
                                                                                                            <p class="card-text"><?= $related['category_name'] ?></p>
                                                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                                                        <div>
                                                                                                                                    <?php if ($related['discount_price']): ?>
                                                                                                                                                <span class="text-danger fw-bold">Rp <?= number_format($related['discount_price'], 0, ',', '.') ?></span>
                                                                                                                                                <span class="text-decoration-line-through text-muted small">Rp <?= number_format($related['price'], 0, ',', '.') ?></span>
                                                                                                                                    <?php else: ?>
                                                                                                                                                <span class="fw-bold">Rp <?= number_format($related['price'], 0, ',', '.') ?></span>
                                                                                                                                    <?php endif; ?>
                                                                                                                        </div>
                                                                                                                        <a href="/pages/product-detail.php?id=<?= $related['id'] ?>" class="btn btn-sm btn-primary">Lihat</a>
                                                                                                            </div>
                                                                                                </div>
                                                                                    </div>
                                                                        </div>
                                                            <?php endforeach; ?>
                                                </div>
                                    </div>
                        </div>
            </div>
</section>

<script>
            // Change main product image when thumbnail is clicked
            function changeMainImage(element) {
                        document.getElementById('main-product-image').src = element.src;
            }

            // Quantity increment/decrement
            document.getElementById('increment-qty').addEventListener('click', function() {
                        const qtyInput = document.querySelector('input[name="quantity"]');
                        const max = parseInt(qtyInput.max);
                        let value = parseInt(qtyInput.value);
                        if (value < max) {
                                    qtyInput.value = value + 1;
                        }
            });

            document.getElementById('decrement-qty').addEventListener('click', function() {
                        const qtyInput = document.querySelector('input[name="quantity"]');
                        let value = parseInt(qtyInput.value);
                        if (value > 1) {
                                    qtyInput.value = value - 1;
                        }
            });

            // Rating stars
            const stars = document.querySelectorAll('.rating-stars .fa-star');
            stars.forEach(star => {
                        star.addEventListener('click', function() {
                                    const rating = parseInt(this.getAttribute('data-rating'));
                                    document.getElementById('rating-value').value = rating;

                                    stars.forEach((s, index) => {
                                                if (index < rating) {
                                                            s.classList.remove('far');
                                                            s.classList.add('fas');
                                                } else {
                                                            s.classList.remove('fas');
                                                            s.classList.add('far');
                                                }
                                    });
                        });
            });

            // Share product
            function shareProduct(socialMedia) {
                        const url = encodeURIComponent(window.location.href);
                        const title = encodeURIComponent(document.title);
                        let shareUrl = '';

                        switch (socialMedia) {
                                    case 'facebook':
                                                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                                                break;
                                    case 'twitter':
                                                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                                                break;
                                    case 'whatsapp':
                                                shareUrl = `https://wa.me/?text=${title}%20${url}`;
                                                break;
                        }

                        window.open(shareUrl, '_blank', 'width=600,height=400');
            }

            // Add to cart form submission
            document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
                        e.preventDefault();

                        const formData = new FormData(this);

                        fetch('/includes/add-to-cart.php', {
                                                method: 'POST',
                                                body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                                if (data.success) {
                                                            // Update cart count
                                                            document.getElementById('cart-count').textContent = data.cart_count;

                                                            // Show success message
                                                            alert('Produk berhasil ditambahkan ke keranjang!');
                                                } else {
                                                            alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                                                }
                                    })
                                    .catch(error => {
                                                console.error('Error:', error);
                                                alert('Terjadi kesalahan. Silakan coba lagi.');
                                    });
            });

            // Add to wishlist
            document.getElementById('add-to-wishlist').addEventListener('click', function() {
                        const productId = this.getAttribute('data-product-id');

                        fetch('/includes/add-to-wishlist.php', {
                                                method: 'POST',
                                                headers: {
                                                            'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify({
                                                            product_id: productId
                                                })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                                if (data.success) {
                                                            this.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                                                            alert('Produk berhasil ditambahkan ke wishlist!');
                                                } else {
                                                            alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                                                }
                                    })
                                    .catch(error => {
                                                console.error('Error:', error);
                                                alert('Terjadi kesalahan. Silakan coba lagi.');
                                    });
            });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>