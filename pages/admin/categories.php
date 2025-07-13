<?php
$form_action = 'create';
$category_id = '';
$category_name = '';
$category_slug = '';
$page_title = 'Tambah Kategori Baru';
$message = '';

function create_slug($string)
{
            $string = strtolower(trim($string));
            $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
            $string = preg_replace('/-+/', '-', $string);
            return rtrim($string, '-');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category_name = mysqli_real_escape_string($conn, $_POST['name']);
            if (empty($category_slug)) {
                        $category_slug = create_slug($category_name);
            }

            if ($_POST['action'] == 'create') {
                        $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, slug) VALUES (?, ?)");
                        mysqli_stmt_bind_param($stmt, "ss", $category_name, $category_slug);
                        if (mysqli_stmt_execute($stmt)) {
                                    $message = "<div class='alert alert-success'>Kategori baru berhasil ditambahkan.</div>";
                        }
            } elseif ($_POST['action'] == 'update') {
                        $category_id = (int)$_POST['category_id'];
                        $stmt = mysqli_prepare($conn, "UPDATE categories SET name = ?, slug = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, "ssi", $category_name, $category_slug, $category_id);
                        if (mysqli_stmt_execute($stmt)) {
                                    $message = "<div class='alert alert-success'>Kategori berhasil diperbarui.</div>";
                        }
            }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $category_id_to_delete = (int)$_GET['id'];
            $product_check_stmt = mysqli_prepare($conn, "SELECT COUNT(id) as total FROM products WHERE category_id = ?");
            mysqli_stmt_bind_param($product_check_stmt, "i", $category_id_to_delete);
            mysqli_stmt_execute($product_check_stmt);
            $product_count_result = mysqli_stmt_get_result($product_check_stmt);
            $product_count = mysqli_fetch_assoc($product_count_result)['total'];

            if ($product_count > 0) {
                        $message = "<div class='alert alert-danger'>Gagal menghapus! Kategori ini sedang digunakan oleh $product_count produk.</div>";
            } else {
                        $delete_stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
                        mysqli_stmt_bind_param($delete_stmt, "i", $category_id_to_delete);
                        if (mysqli_stmt_execute($delete_stmt)) {
                                    $message = "<div class='alert alert-success'>Kategori berhasil dihapus.</div>";
                        }
            }
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $category_id = (int)$_GET['id'];
            $form_action = 'update';
            $page_title = 'Edit Kategori';

            $stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $category_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $category_data = mysqli_fetch_assoc($result);
            $category_name = $category_data['name'];
            $category_slug = $category_data['slug'];
}

$all_categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>

<?= $message ?>
<div class="row">
            <div class="col-lg-8">
                        <div class="card content-card">
                                    <div class="card-header">
                                                <h5 class="mb-0">Daftar Kategori</h5>
                                    </div>
                                    <div class="card-body">
                                                <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                        <thead>
                                                                                    <tr>
                                                                                                <th>No</th>
                                                                                                <th>Nama Kategori</th>
                                                                                                <th>Slug</th>
                                                                                                <th class="text-center">Aksi</th>
                                                                                    </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php
                                                                                    $no = 1;
                                                                                    while ($cat = mysqli_fetch_assoc($all_categories_result)):
                                                                                    ?>
                                                                                                <tr>
                                                                                                            <td><?= $no++ ?></td>
                                                                                                            <td><?= htmlspecialchars($cat['name']) ?></td>
                                                                                                            <td><?= htmlspecialchars($cat['slug']) ?></td>
                                                                                                            <td class="d-flex gap-2 justify-content-end">
                                                                                                                        <a href="<?= BASE_URL ?>admin?page=categories&action=edit&id=<?= $cat['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-pencil"></i></a>
                                                                                                                        <a href="<?= BASE_URL ?>admin?page=categories&action=delete&id=<?= $cat['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus kategori ini?');"><i class="fa-solid fa-trash"></i></a>
                                                                                                            </td>
                                                                                                </tr>
                                                                                    <?php endwhile; ?>
                                                                        </tbody>
                                                            </table>
                                                </div>
                                    </div>
                        </div>
            </div>
            <div class="col-lg-4">
                        <div class="card content-card">
                                    <div class="card-header">
                                                <h5 class="mb-0"><?= $page_title ?></h5>
                                    </div>
                                    <div class="card-body">
                                                <form action="<?= BASE_URL ?>admin?page=categories" method="POST">
                                                            <input type="hidden" name="action" value="<?= $form_action ?>">
                                                            <?php if ($form_action == 'update'): ?>
                                                                        <input type="hidden" name="category_id" value="<?= $category_id ?>">
                                                            <?php endif; ?>

                                                            <div class="mb-3">
                                                                        <label for="name" class="form-label">Nama Kategori</label>
                                                                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category_name) ?>" required>
                                                            </div>
                                                            <div class="d-grid">
                                                                        <button type="submit" class="btn btn-primary"><?= ($form_action == 'update') ? 'Simpan Perubahan' : 'Tambah Kategori' ?></button>
                                                                        <?php if ($form_action == 'update'): ?>
                                                                                    <a href="<?= BASE_URL ?>admin?page=categories" class="btn btn-light mt-2">Batal</a>
                                                                        <?php endif; ?>
                                                            </div>
                                                </form>
                                    </div>
                        </div>
            </div>
</div>