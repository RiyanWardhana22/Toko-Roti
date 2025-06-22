<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/navbar.php';

if (!isAdmin()) {
            redirect('/');
}

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$total_products = countProducts();
$total_pages = ceil($total_products / $per_page);

// Validate current page
if ($current_page < 1) {
            $current_page = 1;
} elseif ($current_page > $total_pages && $total_pages > 0) {
            $current_page = $total_pages;
}

$offset = ($current_page - 1) * $per_page;
$products = getProducts($per_page, $offset);

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
            $products = searchProducts($search, $per_page, $offset);
            $total_products = countSearchProducts($search);
            $total_pages = ceil($total_products / $per_page);
}
?>

<div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Manajemen Produk</h1>
                        <a href="/admin/products/add.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah Produk
                        </a>
            </div>

            <!-- Search and Filter -->
            <div class="card mb-4">
                        <div class="card-body">
                                    <form method="get" class="row g-3">
                                                <div class="col-md-8">
                                                            <div class="input-group">
                                                                        <input type="text" class="form-control" placeholder="Cari produk..." name="search" value="<?= htmlspecialchars($search) ?>">
                                                                        <button class="btn btn-outline-secondary" type="submit">
                                                                                    <i class="fas fa-search"></i>
                                                                        </button>
                                                            </div>
                                                </div>
                                                <div class="col-md-4">
                                                            <select class="form-select" name="category">
                                                                        <option value="">Semua Kategori</option>
                                                                        <?php foreach (getCategories() as $category): ?>
                                                                                    <option value="<?= $category['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : '' ?>>
                                                                                                <?= $category['name'] ?>
                                                                                    </option>
                                                                        <?php endforeach; ?>
                                                            </select>
                                                </div>
                                    </form>
                        </div>
            </div>

            <!-- Products Table -->
            <div class="card shadow">
                        <div class="card-body">
                                    <div class="table-responsive">
                                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                                            <thead class="table-light">
                                                                        <tr>
                                                                                    <th width="50px">#</th>
                                                                                    <th>Produk</th>
                                                                                    <th>Kategori</th>
                                                                                    <th>Harga</th>
                                                                                    <th>Stok</th>
                                                                                    <th>Status</th>
                                                                                    <th width="120px">Aksi</th>
                                                                        </tr>
                                                            </thead>
                                                            <tbody>
                                                                        <?php if (empty($products)): ?>
                                                                                    <tr>
                                                                                                <td colspan="7" class="text-center py-4">Tidak ada produk ditemukan</td>
                                                                                    </tr>
                                                                        <?php else: ?>
                                                                                    <?php foreach ($products as $index => $product): ?>
                                                                                                <tr>
                                                                                                            <td><?= $index + 1 + $offset ?></td>
                                                                                                            <td>
                                                                                                                        <div class="d-flex align-items-center">
                                                                                                                                    <img src="/assets/uploads/<?= $product['image'] ?>"
                                                                                                                                                class="img-thumbnail me-3"
                                                                                                                                                width="60"
                                                                                                                                                alt="<?= $product['name'] ?>">
                                                                                                                                    <div>
                                                                                                                                                <h6 class="mb-0"><?= $product['name'] ?></h6>
                                                                                                                                                <small class="text-muted"><?= shortenDescription($product['description'], 50) ?></small>
                                                                                                                                    </div>
                                                                                                                        </div>
                                                                                                            </td>
                                                                                                            <td><?= $product['category_name'] ?></td>
                                                                                                            <td>
                                                                                                                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                                                                                                                        <?php if ($product['discount_price']): ?>
                                                                                                                                    <br><small class="text-danger">
                                                                                                                                                <s>Rp <?= number_format($product['discount_price'], 0, ',', '.') ?></s>
                                                                                                                                    </small>
                                                                                                                        <?php endif; ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                        <span class="badge bg-<?= $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger') ?>">
                                                                                                                                    <?= $product['stock'] ?>
                                                                                                                        </span>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                        <span class="badge bg-<?= $product['is_featured'] ? 'primary' : 'secondary' ?>">
                                                                                                                                    <?= $product['is_featured'] ? 'Featured' : 'Regular' ?>
                                                                                                                        </span>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                        <div class="d-flex gap-2">
                                                                                                                                    <a href="/admin/products/edit.php?id=<?= $product['id'] ?>"
                                                                                                                                                class="btn btn-sm btn-warning"
                                                                                                                                                title="Edit">
                                                                                                                                                <i class="fas fa-edit"></i>
                                                                                                                                    </a>
                                                                                                                                    <button class="btn btn-sm btn-danger delete-product"
                                                                                                                                                data-id="<?= $product['id'] ?>"
                                                                                                                                                title="Hapus">
                                                                                                                                                <i class="fas fa-trash"></i>
                                                                                                                                    </button>
                                                                                                                        </div>
                                                                                                            </td>
                                                                                                </tr>
                                                                                    <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                            </tbody>
                                                </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($total_pages > 1): ?>
                                                <nav aria-label="Page navigation" class="mt-4">
                                                            <ul class="pagination justify-content-center">
                                                                        <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                                                                                    <a class="page-link"
                                                                                                href="?page=<?= $current_page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                                                                                aria-label="Previous">
                                                                                                <span aria-hidden="true">&laquo;</span>
                                                                                    </a>
                                                                        </li>

                                                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                                                                                <a class="page-link"
                                                                                                            href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                                                                            <?= $i ?>
                                                                                                </a>
                                                                                    </li>
                                                                        <?php endfor; ?>

                                                                        <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                                                                                    <a class="page-link"
                                                                                                href="?page=<?= $current_page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                                                                                aria-label="Next">
                                                                                                <span aria-hidden="true">&raquo;</span>
                                                                                    </a>
                                                                        </li>
                                                            </ul>
                                                </nav>
                                    <?php endif; ?>
                        </div>
            </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                        <div class="modal-content">
                                    <div class="modal-header">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                    <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="button" class="btn btn-danger" id="confirm-delete">Hapus</button>
                                    </div>
                        </div>
            </div>
</div>

<script>
            // Delete product
            let productIdToDelete = null;
            const deleteButtons = document.querySelectorAll('.delete-product');
            const confirmDeleteBtn = document.getElementById('confirm-delete');

            deleteButtons.forEach(button => {
                        button.addEventListener('click', function() {
                                    productIdToDelete = this.getAttribute('data-id');
                                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                                    modal.show();
                        });
            });

            confirmDeleteBtn.addEventListener('click', function() {
                        if (productIdToDelete) {
                                    fetch(`/admin/products/delete.php?id=${productIdToDelete}`, {
                                                            method: 'DELETE'
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                            if (data.success) {
                                                                        window.location.reload();
                                                            } else {
                                                                        alert(data.message || 'Terjadi kesalahan saat menghapus produk.');
                                                            }
                                                })
                                                .catch(error => {
                                                            console.error('Error:', error);
                                                            alert('Terjadi kesalahan saat menghapus produk.');
                                                });
                        }
            });
</script>

<?php
require_once __DIR__ . '/../../../includes/footer.php';
?>