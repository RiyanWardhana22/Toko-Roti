<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';
?>

<!-- Hero Slider -->
<div id="heroSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                        <div class="carousel-item active">
                                    <img src="/assets/images/slider1.jpg" class="d-block w-100" alt="Promo 1">
                                    <div class="carousel-caption d-none d-md-block">
                                                <h5>Promo Spesial Bulan Ini</h5>
                                                <p>Diskon 20% untuk semua produk roti tawar</p>
                                    </div>
                        </div>
                        <div class="carousel-item">
                                    <img src="/assets/images/slider2.jpg" class="d-block w-100" alt="Promo 2">
                                    <div class="carousel-caption d-none d-md-block">
                                                <h5>Kue Ulang Tahun Custom</h5>
                                                <p>Pesan sekarang untuk acara spesial Anda</p>
                                    </div>
                        </div>
                        <div class="carousel-item">
                                    <img src="/assets/images/slider3.jpg" class="d-block w-100" alt="Promo 3">
                                    <div class="carousel-caption d-none d-md-block">
                                                <h5>Produk Baru Setiap Minggu</h5>
                                                <p>Cek koleksi terbaru kami</p>
                                    </div>
                        </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
            </button>
</div>

<!-- Featured Products -->
<section class="py-5">
            <div class="container">
                        <h2 class="text-center mb-5">Produk Unggulan</h2>
                        <div class="row">
                                    <?php foreach (getProducts(4, null, true) as $product): ?>
                                                <div class="col-md-3 mb-4">
                                                            <div class="card h-100">
                                                                        <img src="/assets/uploads/<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                                                                        <div class="card-body">
                                                                                    <h5 class="card-title"><?= $product['name'] ?></h5>
                                                                                    <p class="card-text"><?= $product['category_name'] ?></p>
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                                <div>
                                                                                                            <?php if ($product['discount_price']): ?>
                                                                                                                        <span class="text-danger fw-bold">Rp <?= number_format($product['discount_price'], 0, ',', '.') ?></span>
                                                                                                                        <span class="text-decoration-line-through text-muted small">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                                                                            <?php else: ?>
                                                                                                                        <span class="fw-bold">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                                                                            <?php endif; ?>
                                                                                                </div>
                                                                                                <a href="/pages/product-detail.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Lihat</a>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                </div>
                                    <?php endforeach; ?>
                        </div>
            </div>
</section>

<!-- New Arrivals -->
<section class="py-5 bg-light">
            <div class="container">
                        <h2 class="text-center mb-5">Produk Terbaru</h2>
                        <div class="row">
                                    <?php foreach (getProducts(8) as $product): ?>
                                                <div class="col-md-3 mb-4">
                                                            <div class="card h-100">
                                                                        <img src="/assets/uploads/<?= $product['image'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                                                                        <div class="card-body">
                                                                                    <h5 class="card-title"><?= $product['name'] ?></h5>
                                                                                    <p class="card-text"><?= $product['category_name'] ?></p>
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                                <div>
                                                                                                            <?php if ($product['discount_price']): ?>
                                                                                                                        <span class="text-danger fw-bold">Rp <?= number_format($product['discount_price'], 0, ',', '.') ?></span>
                                                                                                                        <span class="text-decoration-line-through text-muted small">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                                                                            <?php else: ?>
                                                                                                                        <span class="fw-bold">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                                                                            <?php endif; ?>
                                                                                                </div>
                                                                                                <a href="/pages/product-detail.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Lihat</a>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                </div>
                                    <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                                    <a href="/pages/products.php" class="btn btn-outline-primary">Lihat Semua Produk</a>
                        </div>
            </div>
</section>

<!-- Categories -->
<section class="py-5">
            <div class="container">
                        <h2 class="text-center mb-5">Kategori Produk</h2>
                        <div class="row">
                                    <?php foreach (getCategories() as $category): ?>
                                                <div class="col-md-3 mb-4">
                                                            <div class="card h-100">
                                                                        <a href="/pages/products.php?category=<?= $category['id'] ?>">
                                                                                    <img src="/assets/uploads/<?= $category['image'] ?>" class="card-img-top" alt="<?= $category['name'] ?>">
                                                                                    <div class="card-body text-center">
                                                                                                <h5 class="card-title"><?= $category['name'] ?></h5>
                                                                                    </div>
                                                                        </a>
                                                            </div>
                                                </div>
                                    <?php endforeach; ?>
                        </div>
            </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-light">
            <div class="container">
                        <h2 class="text-center mb-5">Testimoni Pelanggan</h2>
                        <div class="row">
                                    <div class="col-md-4 mb-4">
                                                <div class="card h-100">
                                                            <div class="card-body text-center">
                                                                        <div class="mb-3">
                                                                                    <i class="fas fa-quote-left fa-2x text-muted"></i>
                                                                        </div>
                                                                        <p class="card-text">"Roti dari Toko Roti Enak selalu segar dan enak. Keluarga saya sangat menyukainya!"</p>
                                                                        <div class="mt-3">
                                                                                    <img src="/assets/images/user1.jpg" class="rounded-circle" width="60" alt="Pelanggan 1">
                                                                                    <h5 class="mt-2 mb-0">Budi Santoso</h5>
                                                                                    <div class="text-warning">
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                                <div class="card h-100">
                                                            <div class="card-body text-center">
                                                                        <div class="mb-3">
                                                                                    <i class="fas fa-quote-left fa-2x text-muted"></i>
                                                                        </div>
                                                                        <p class="card-text">"Kue ulang tahun custom untuk anak saya sangat bagus dan rasanya enak. Terima kasih!"</p>
                                                                        <div class="mt-3">
                                                                                    <img src="/assets/images/user2.jpg" class="rounded-circle" width="60" alt="Pelanggan 2">
                                                                                    <h5 class="mt-2 mb-0">Ani Wijaya</h5>
                                                                                    <div class="text-warning">
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star-half-alt"></i>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                                <div class="card h-100">
                                                            <div class="card-body text-center">
                                                                        <div class="mb-3">
                                                                                    <i class="fas fa-quote-left fa-2x text-muted"></i>
                                                                        </div>
                                                                        <p class="card-text">"Pelayanan cepat dan rotinya selalu fresh. Sudah langganan lebih dari 2 tahun."</p>
                                                                        <div class="mt-3">
                                                                                    <img src="/assets/images/user3.jpg" class="rounded-circle" width="60" alt="Pelanggan 3">
                                                                                    <h5 class="mt-2 mb-0">Dewi Lestari</h5>
                                                                                    <div class="text-warning">
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                                <i class="fas fa-star"></i>
                                                                                    </div>
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>