<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/functions.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

// Dapatkan produk berdasarkan filter
$products = getFilteredProducts($category_id, $search, $sort, $min_price, $max_price);
?>

<!-- Product Listing -->
<section class="py-5">
            <div class="container">
                        <div class="row">
                                    <!-- Sidebar Filter -->
                                    <div class="col-md-3">
                                                <div class="card mb-4">
                                                            <div class="card-header bg-primary text-white">
                                                                        <h5 class="mb-0">Filter</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                        <form method="get" action="">
                                                                                    <!-- Kategori -->
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Kategori</label>
                                                                                                <select class="form-select" name="category">
                                                                                                            <option value="">Semua Kategori</option>
                                                                                                            <?php foreach (getCategories() as $cat): ?>
                                                                                                                        <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                                                                                                                                    <?= $cat['name'] ?>
                                                                                                                        </option>
                                                                                                            <?php endforeach; ?>
                                                                                                </select>
                                                                                    </div>

                                                                                    <!-- Rentang Harga -->
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Rentang Harga</label>
                                                                                                <div class="input-group mb-2">
                                                                                                            <span class="input-group-text">Rp</span>
                                                                                                            <input type="number" class="form-control" placeholder="Min" name="min_price" value="<?= $min_price ?>">
                                                                                                </div>
                                                                                                <div class="input-group">
                                                                                                            <span class="input-group-text">Rp</span>
                                                                                                            <input type="number" class="form-control" placeholder="Max" name="max_price" value="<?= $max_price ?>">
                                                                                                </div>
                                                                                    </div>

                                                                                    <!-- Urutkan -->
                                                                                    <div class="mb-3">
                                                                                                <label class="form-label">Urutkan</label>
                                                                                                <select class="form-select" name="sort">
                                                                                                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                                                                                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                                                                                                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                                                                                            <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>Terpopuler</option>
                                                                                                </select>
                                                                                    </div>

                                                                                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                                                                        </form>
                                                            </div>
                                                </div>
                                    </div>

                                    <!-- Product List -->
                                    <div class="col-md-9">
                                                <div class="row mb-4">
                                                            <div class="col-md-6">
                                                                        <h2><?= $category_id ? getCategoryName($category_id) : 'Semua Produk' ?></h2>
                                                            </div>
                                                            <div class="col-md-6">
                                                                        <form method="get" action="">
                                                                                    <div class="input-group">
                                                                                                <input type="text" class="form-control" placeholder="Cari produk..." name="search" value="<?= htmlspecialchars($search) ?>">
                                                                                                <button class="btn btn-outline-secondary" type="submit">
                                                                                                            <i class="fas fa-search"></i>
                                                                                                </button>
                                                                                    </div>
                                                                                    <?php if ($category_id): ?>
                                                                                                <input type="hidden" name="category" value="<?= $category_id ?>">
                                                                                    <?php endif; ?>
                                                                        </form>
                                                            </div>
                                                </div>

                                                <!-- View Toggle -->
                                                <div class="mb-3 d-flex justify-content-end">
                                                            <div class="btn-group" role="group">
                                                                        <button type="button" class="btn btn-outline-secondary active" id="grid-view">
                                                                                    <i class="fas fa-th"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-outline-secondary" id="list-view">
                                                                                    <i class="fas fa-list"></i>
                                                                        </button>
                                                            </div>
                                                </div>

                                                <!-- Products Grid View -->
                                                <div class="row" id="products-grid">
                                                            <?php foreach ($products as $product): ?>
                                                                        <div class="col-md-4 mb-4">
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

                                                <!-- Products List View (hidden by default) -->
                                                <div class="d-none" id="products-list">
                                                            <?php foreach ($products as $product): ?>
                                                                        <div class="card mb-3">
                                                                                    <div class="row g-0">
                                                                                                <div class="col-md-3">
                                                                                                            <img src="/assets/uploads/<?= $product['image'] ?>" class="img-fluid rounded-start" alt="<?= $product['name'] ?>">
                                                                                                </div>
                                                                                                <div class="col-md-9">
                                                                                                            <div class="card-body">
                                                                                                                        <h5 class="card-title"><?= $product['name'] ?></h5>
                                                                                                                        <p class="card-text"><?= $product['category_name'] ?></p>
                                                                                                                        <p class="card-text"><?= shortenDescription($product['description'], 150) ?></p>
                                                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                                                                    <div>
                                                                                                                                                <?php if ($product['discount_price']): ?>
                                                                                                                                                            <span class="text-danger fw-bold">Rp <?= number_format($product['discount_price'], 0, ',', '.') ?></span>
                                                                                                                                                            <span class="text-decoration-line-through text-muted small">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                                                                                                                <?php else: ?>
                                                                                                                                                            <span class="fw-bold">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                                                                                                                                <?php endif; ?>
                                                                                                                                    </div>
                                                                                                                                    <div>
                                                                                                                                                <a href="/pages/product-detail.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Lihat Detail</a>
                                                                                                                                                <button class="btn btn-sm btn-outline-secondary add-to-cart" data-id="<?= $product['id'] ?>">
                                                                                                                                                            <i class="fas fa-cart-plus"></i> Tambah
                                                                                                                                                </button>
                                                                                                                                    </div>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                </div>
                                                                                    </div>
                                                                        </div>
                                                            <?php endforeach; ?>
                                                </div>

                                                <!-- Pagination -->
                                                <nav aria-label="Page navigation" class="mt-4">
                                                            <ul class="pagination justify-content-center">
                                                                        <li class="page-item disabled">
                                                                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                                                        </li>
                                                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                                        <li class="page-item">
                                                                                    <a class="page-link" href="#">Next</a>
                                                                        </li>
                                                            </ul>
                                                </nav>
                                    </div>
                        </div>
            </div>
</section>

<script>
            // Toggle between grid and list view
            document.getElementById('grid-view').addEventListener('click', function() {
                        document.getElementById('products-grid').classList.remove('d-none');
                        document.getElementById('products-list').classList.add('d-none');
                        this.classList.add('active');
                        document.getElementById('list-view').classList.remove('active');
            });

            document.getElementById('list-view').addEventListener('click', function() {
                        document.getElementById('products-grid').classList.add('d-none');
                        document.getElementById('products-list').classList.remove('d-none');
                        this.classList.add('active');
                        document.getElementById('grid-view').classList.remove('active');
            });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>