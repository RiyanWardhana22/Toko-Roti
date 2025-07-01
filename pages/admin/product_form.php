<?php
$is_edit_mode = false;
$product = [
            'id' => '',
            'name' => '',
            'category_id' => '',
            'price' => '',
            'weight' => '',
            'stock' => '',
            'description' => '',
            'image_url' => ''
];
$page_title = "Tambah Produk Baru";

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $is_edit_mode = true;
            $product_id = (int)$_GET['id'];
            $page_title = "Edit Produk";

            $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
}

$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>

<div class="card content-card">
            <div class="card-header">
                        <h5 class="mb-0"><?= $page_title ?></h5>
            </div>
            <div class="card-body">
                        <form action="<?= BASE_URL ?>app/product_action.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="<?= $is_edit_mode ? 'update' : 'create' ?>">
                                    <?php if ($is_edit_mode): ?>
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <?php endif; ?>

                                    <div class="row">
                                                <div class="col-md-8">
                                                            <div class="mb-3">
                                                                        <label for="name" class="form-label">Nama Produk</label>
                                                                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label for="description" class="form-label">Deskripsi</label>
                                                                        <textarea class="form-control" id="description" name="description" rows="12"><?= htmlspecialchars($product['description']) ?></textarea>
                                                            </div>
                                                </div>
                                                <div class="col-md-4">
                                                            <div class="mb-3">
                                                                        <label for="category_id" class="form-label">Kategori</label>
                                                                        <select class="form-select" id="category_id" name="category_id" required>
                                                                                    <option value="">Pilih Kategori</option>
                                                                                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                                                                                <option value="<?= $category['id'] ?>" <?= ($product['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                                                                                            <?= htmlspecialchars($category['name']) ?>
                                                                                                </option>
                                                                                    <?php endwhile; ?>
                                                                        </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label for="price" class="form-label">Harga</label>
                                                                        <input type="number" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label for="stock" class="form-label">Stok</label>
                                                                        <input type="number" class="form-control" id="stock" name="stock" value="<?= $product['stock'] ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label for="image" class="form-label">Gambar Produk</label>
                                                                        <input class="form-control" type="file" id="image" name="image" <?= !$is_edit_mode ? 'required' : '' ?>>
                                                                        <?php if ($is_edit_mode && !empty($product['image_url'])): ?>
                                                                                    <div class="mt-2">
                                                                                                <img src="<?= BASE_URL ?>assets/images/<?= $product['image_url'] ?>" width="100" class="rounded" alt="">
                                                                                                <p class="form-text">Gambar saat ini. Kosongkan jika tidak ingin mengubah.</p>
                                                                                    </div>
                                                                        <?php endif; ?>
                                                            </div>
                                                </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-end">
                                                <a href="<?= BASE_URL ?>admin?page=products" class="btn btn-secondary me-2">Batal</a>
                                                <button type="submit" class="btn btn-primary">Simpan Produk</button>
                                    </div>
                        </form>
            </div>
</div>