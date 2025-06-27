<?php
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$current_category_name = "Semua Produk";
if (!empty($_GET['kategori'])) {
            $slug = $_GET['kategori'];
            $cat_res = mysqli_query($conn, "SELECT name FROM categories WHERE slug = '$slug'");
            if ($cat_data = mysqli_fetch_assoc($cat_res)) {
                        $current_category_name = $cat_data['name'];
            }
}

$base_query = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id";
$conditions = [];
$params = [];
$types = '';

if (!empty($_GET['kategori'])) {
            $conditions[] = 'c.slug = ?';
            $params[] = $_GET['kategori'];
            $types .= 's';
}

if (!empty($_GET['q'])) {
            $conditions[] = 'p.name LIKE ?';
            $params[] = '%' . $_GET['q'] . '%';
            $types .= 's';
}

if (count($conditions) > 0) {
            $base_query .= " WHERE " . implode(' AND ', $conditions);
}

$order_by = " ORDER BY p.created_at DESC";
if (!empty($_GET['urutkan'])) {
            switch ($_GET['urutkan']) {
                        case 'termurah':
                                    $order_by = " ORDER BY p.price ASC";
                                    break;
                        case 'termahal':
                                    $order_by = " ORDER BY p.price DESC";
                                    break;
                        case 'terbaru':
                                    $order_by = " ORDER BY p.created_at DESC";
                                    break;
            }
}
$base_query .= $order_by;

$stmt = mysqli_prepare($conn, $base_query);
if ($types) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result_products = mysqli_stmt_get_result($stmt);
?>

<main>
            <div class="container">
                        <div class="row mt-3">
                                    <div class="col-lg-3">
                                                <div class="filter-sidebar">
                                                            <h5>Kategori Produk</h5>
                                                            <ul class="list-group">
                                                                        <a href="<?= BASE_URL ?>produk" class="list-group-item list-group-item-action <?= empty($_GET['kategori']) ? 'active' : '' ?>">Semua Kategori</a>
                                                                        <?php mysqli_data_seek($categories, 0);
                                                                        while ($category = mysqli_fetch_assoc($categories)): ?>
                                                                                    <a href="<?= BASE_URL ?>produk?kategori=<?= $category['slug'] ?>" class="list-group-item list-group-item-action <?= (isset($_GET['kategori']) && $_GET['kategori'] == $category['slug']) ? 'active' : '' ?>">
                                                                                                <?= htmlspecialchars($category['name']) ?>
                                                                                    </a>
                                                                        <?php endwhile; ?>
                                                            </ul>
                                                </div>
                                    </div>

                                    <div class="col-lg-9">
                                                <div class="card mb-4">
                                                            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
                                                                        <div class="mb-3 mb-md-0">
                                                                                    <p class="mb-0">Menampilkan <strong><?= mysqli_num_rows($result_products) ?></strong> produk.</p>
                                                                        </div>
                                                                        <div class="d-flex">
                                                                                    <form action="<?= BASE_URL ?>produk" method="GET" class="me-2">
                                                                                                <?php if (!empty($_GET['kategori'])): ?><input type="hidden" name="kategori" value="<?= $_GET['kategori'] ?>"><?php endif; ?>
                                                                                                <div class="input-group">
                                                                                                            <input type="text" class="form-control" name="q" placeholder="Cari produk..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                                                                                                            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                                                                                                </div>
                                                                                    </form>
                                                                                    <form action="<?= BASE_URL ?>produk" method="GET" id="form-urutkan">
                                                                                                <?php if (!empty($_GET['kategori'])): ?><input type="hidden" name="kategori" value="<?= $_GET['kategori'] ?>"><?php endif; ?>
                                                                                                <?php if (!empty($_GET['q'])): ?><input type="hidden" name="q" value="<?= $_GET['q'] ?>"><?php endif; ?>
                                                                                                <select name="urutkan" class="form-select" onchange="document.getElementById('form-urutkan').submit()">
                                                                                                            <option value="terbaru" <?= (isset($_GET['urutkan']) && $_GET['urutkan'] == 'terbaru') ? 'selected' : '' ?>>Urutkan: Terbaru</option>
                                                                                                            <option value="termurah" <?= (isset($_GET['urutkan']) && $_GET['urutkan'] == 'termurah') ? 'selected' : '' ?>>Harga: Termurah</option>
                                                                                                            <option value="termahal" <?= (isset($_GET['urutkan']) && $_GET['urutkan'] == 'termahal') ? 'selected' : '' ?>>Harga: Termahal</option>
                                                                                                </select>
                                                                                    </form>
                                                                        </div>
                                                            </div>
                                                </div>

                                                <div class="row g-4">
                                                            <?php if (mysqli_num_rows($result_products) > 0) : ?>
                                                                        <?php while ($product = mysqli_fetch_assoc($result_products)) : ?>
                                                                                    <?php include 'parts/product_card.php'; ?>
                                                                        <?php endwhile; ?>
                                                            <?php else : ?>
                                                                        <div class="col-12">
                                                                                    <div class="alert alert-warning text-center">
                                                                                                <h4>Produk Tidak Ditemukan</h4>
                                                                                                <p>Maaf, tidak ada produk yang cocok dengan kriteria Anda. Silakan coba filter atau kata kunci lain.</p>
                                                                                    </div>
                                                                        </div>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>