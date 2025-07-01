<?php
function handle_logo_upload($file_input_name)
{
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
                        $upload_dir = __DIR__ . '/../../assets/images/logos/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                        $file_name = "logo-" . time() . '-' . basename($_FILES[$file_input_name]['name']);
                        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $upload_dir . $file_name)) return $file_name;
            }
            return null;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $details = mysqli_real_escape_string($conn, $_POST['details']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($_POST['action'] == 'create') {
                        $logo = handle_logo_upload('logo');
                        $stmt = mysqli_prepare($conn, "INSERT INTO payment_methods (method_name, account_details, logo_url, is_active) VALUES (?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, "sssi", $name, $details, $logo, $is_active);
                        if (mysqli_stmt_execute($stmt)) $message = "<div class='alert alert-success'>Metode pembayaran berhasil ditambahkan.</div>";
            } elseif ($_POST['action'] == 'update') {
                        $id = (int)$_POST['id'];
                        $logo = handle_logo_upload('logo');
                        if ($logo) {
                                    $stmt = mysqli_prepare($conn, "UPDATE payment_methods SET method_name=?, account_details=?, logo_url=?, is_active=? WHERE id=?");
                                    mysqli_stmt_bind_param($stmt, "sssii", $name, $details, $logo, $is_active, $id);
                        } else {
                                    $stmt = mysqli_prepare($conn, "UPDATE payment_methods SET method_name=?, account_details=?, is_active=? WHERE id=?");
                                    mysqli_stmt_bind_param($stmt, "ssii", $name, $details, $is_active, $id);
                        }
                        if (mysqli_stmt_execute($stmt)) $message = "<div class='alert alert-success'>Metode pembayaran berhasil diperbarui.</div>";
            }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            mysqli_query($conn, "DELETE FROM payment_methods WHERE id = $id");
            $message = "<div class='alert alert-success'>Metode pembayaran berhasil dihapus.</div>";
}

$edit_mode = false;
$edit_data = ['id' => '', 'method_name' => '', 'account_details' => '', 'logo_url' => '', 'is_active' => 1];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $edit_mode = true;
            $id = (int)$_GET['id'];
            $res = mysqli_query($conn, "SELECT * FROM payment_methods WHERE id = $id");
            $edit_data = mysqli_fetch_assoc($res);
}

$all_methods = mysqli_query($conn, "SELECT * FROM payment_methods");
?>

<?= $message ?>
<div class="row">
            <div class="col-lg-8">
                        <div class="card content-card">
                                    <div class="card-header">Daftar Metode Pembayaran</div>
                                    <div class="card-body">
                                                <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                        <thead>
                                                                                    <tr>
                                                                                                <th>Logo</th>
                                                                                                <th>Nama Metode</th>
                                                                                                <th>Detail Akun</th>
                                                                                                <th>Status</th>
                                                                                                <th class="text-center">Aksi</th>
                                                                                    </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php while ($row = mysqli_fetch_assoc($all_methods)): ?>
                                                                                                <tr>
                                                                                                            <td>
                                                                                                                        <?php if (!empty($row['logo_url'])): ?>
                                                                                                                                    <img src="<?= BASE_URL ?>assets/images/logos/<?= $row['logo_url'] ?>" height="30" alt="">
                                                                                                                        <?php endif; ?>
                                                                                                            </td>
                                                                                                            <td><?= htmlspecialchars($row['method_name']) ?></td>
                                                                                                            <td><small><?= nl2br(htmlspecialchars($row['account_details'])) ?></small></td>
                                                                                                            <td><span class="badge <?= $row['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                                                                                                            <td class="d-flex text-end gap-2">
                                                                                                                        <a href="?page=settings_payment&action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pencil"></i></a>
                                                                                                                        <a href="?page=settings_payment&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin?')"><i class="fa-solid fa-trash"></i></a>
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
                                    <div class="card-header"><?= $edit_mode ? 'Edit Metode Pembayaran' : 'Tambah Metode Baru' ?></div>
                                    <div class="card-body">
                                                <form method="POST" action="?page=settings_payment" enctype="multipart/form-data">
                                                            <input type="hidden" name="action" value="<?= $edit_mode ? 'update' : 'create' ?>">
                                                            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                                                            <div class="mb-3">
                                                                        <label class="form-label">Nama Metode</label>
                                                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($edit_data['method_name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Detail Akun</label>
                                                                        <textarea class="form-control" name="details" rows="4" required><?= htmlspecialchars($edit_data['account_details']) ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Logo</label>
                                                                        <input type="file" class="form-control" name="logo">
                                                            </div>
                                                            <div class="form-check mb-3">
                                                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $edit_data['is_active'] ? 'checked' : '' ?>>
                                                                        <label class="form-check-label" for="is_active">Aktifkan metode ini</label>
                                                            </div>
                                                            <div class="d-grid">
                                                                        <button type="submit" class="btn btn-primary"><?= $edit_mode ? 'Simpan Perubahan' : 'Tambah' ?></button>
                                                                        <?php if ($edit_mode): ?>
                                                                                    <a href="?page=settings_payment" class="btn btn-light mt-2">Batal</a>
                                                                        <?php endif; ?>
                                                            </div>
                                                </form>
                                    </div>
                        </div>
            </div>
</div>