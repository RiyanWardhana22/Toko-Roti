<?php
if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')) {
            include 'product_form.php';
            return;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $product_id_to_delete = (int)$_GET['id'];
            $image_query = mysqli_query($conn, "SELECT image_url FROM products WHERE id = $product_id_to_delete");
            if ($image_row = mysqli_fetch_assoc($image_query)) {
                        $image_path_to_delete = __DIR__ . '/../../assets/images/' . $image_row['image_url'];
                        if (file_exists($image_path_to_delete) && !empty($image_row['image_url'])) {
                                    unlink($image_path_to_delete);
                        }
            }
            $delete_stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
            mysqli_stmt_bind_param($delete_stmt, "i", $product_id_to_delete);
            if (mysqli_stmt_execute($delete_stmt)) {
                        echo "<div class='alert alert-success'>Produk berhasil dihapus. Halaman akan dimuat ulang.</div>";
                        echo "<meta http-equiv='refresh' content='2;url=" . BASE_URL . "admin?page=products'>";
            }
}

$all_products_result = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
?>

<div class="d-flex justify-content-end mb-3">
            <a href="<?= BASE_URL ?>admin?page=products&action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Produk Baru
            </a>
</div>

<div class="card content-card">
            <div class="card-header">
                        Daftar Produk
            </div>
            <div class="card-body">
                        <div class="table-responsive">
                                    <table class="table table-hover">
                                                <thead>
                                                            <tr>
                                                                        <th scope="col">No</th>
                                                                        <th scope="col">Gambar</th>
                                                                        <th scope="col">Nama Produk</th>
                                                                        <th scope="col">Kategori</th>
                                                                        <th scope="col">Harga</th>
                                                                        <th scope="col">Stok</th>
                                                                        <th scope="col" class="text-end">Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php
                                                            $no = 1;
                                                            if (mysqli_num_rows($all_products_result) > 0):
                                                                        while ($product_row = mysqli_fetch_assoc($all_products_result)):
                                                            ?>
                                                                                    <tr>
                                                                                                <td><?= $no++ ?></td>
                                                                                                <td>
                                                                                                            <?php if (!empty($product_row['image_url'])): ?>
                                                                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($product_row['image_url']) ?>" alt="Gambar Produk" width="50" height="50" style="object-fit: cover; border-radius: 8px;">
                                                                                                            <?php else: ?>
                                                                                                                        <div style="width: 50px; height: 50px; background-color: #eee;" class="d-flex align-items-center justify-content-center">No Img</div>
                                                                                                            <?php endif; ?>
                                                                                                </td>
                                                                                                <td><?= htmlspecialchars($product_row['name']) ?></td>
                                                                                                <td><?= htmlspecialchars($product_row['category_name'] ?? 'N/A') ?></td>
                                                                                                <td>Rp <?= number_format($product_row['price'], 0, ',', '.') ?></td>
                                                                                                <td><?= $product_row['stock'] ?></td>
                                                                                                <td class="text-end">
                                                                                                            <a href="<?= BASE_URL ?>admin?page=products&action=edit&id=<?= $product_row['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-pencil"></i></a>
                                                                                                            <a href="<?= BASE_URL ?>admin?page=products&action=delete&id=<?= $product_row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus produk ini secara permanen?');"><i class="fa-solid fa-trash"></i></a>
                                                                                                </td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else: ?>
                                                                        <tr>
                                                                                    <td colspan="7" class="text-center">Belum ada produk yang ditambahkan.</td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>
</div>