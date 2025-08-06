<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            echo '<div class="alert alert-danger text-center">Anda tidak memiliki hak akses untuk melihat halaman ini.</div>';
            return;
}

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

function handle_qris_upload($file_input_name)
{
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
                        $upload_dir = __DIR__ . '/../../assets/images/qris/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                        $file_name = "qris-" . time() . '-' . basename($_FILES[$file_input_name]['name']);
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
                        $qris_image = handle_qris_upload('qris_image');
                        $stmt = mysqli_prepare($conn, "INSERT INTO payment_methods (method_name, account_details, logo_url, qris_image_url, is_active) VALUES (?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, "ssssi", $name, $details, $logo, $qris_image, $is_active);
            } elseif ($_POST['action'] == 'update') {
                        $id = (int)$_POST['id'];
                        $logo = handle_logo_upload('logo');
                        $qris_image = handle_qris_upload('qris_image');

                        $sql = "UPDATE payment_methods SET method_name=?, account_details=?, is_active=?";
                        $params = [$name, $details, $is_active];
                        $types = "ssi";

                        if ($logo) {
                                    $sql .= ", logo_url=?";
                                    $params[] = $logo;
                                    $types .= "s";
                        }
                        if ($qris_image) {
                                    $sql .= ", qris_image_url=?";
                                    $params[] = $qris_image;
                                    $types .= "s";
                        }

                        $sql .= " WHERE id=?";
                        $params[] = $id;
                        $types .= "i";

                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            if (mysqli_stmt_execute($stmt)) $message = "<div class='alert alert-success'>Metode pembayaran berhasil diproses.</div>";
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $res = mysqli_query($conn, "SELECT logo_url, qris_image_url FROM payment_methods WHERE id = $id");
            if ($row = mysqli_fetch_assoc($res)) {
                        if (!empty($row['logo_url'])) unlink(__DIR__ . '/../../assets/images/logos/' . $row['logo_url']);
                        if (!empty($row['qris_image_url'])) unlink(__DIR__ . '/../../assets/images/qris/' . $row['qris_image_url']);
            }
            mysqli_query($conn, "DELETE FROM payment_methods WHERE id = $id");
            $message = "<div class='alert alert-success'>Metode pembayaran berhasil dihapus.</div>";
}

$edit_mode = false;
$edit_data = ['id' => '', 'method_name' => '', 'account_details' => '', 'logo_url' => '', 'qris_image_url' => '', 'is_active' => 1];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $edit_mode = true;
            $id = (int)$_GET['id'];
            $res = mysqli_query($conn, "SELECT * FROM payment_methods WHERE id = $id");
            $edit_data = mysqli_fetch_assoc($res);
}

$all_methods = mysqli_query($conn, "SELECT * FROM payment_methods ORDER BY id DESC");
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
                                                                                                            <td class="d-flex justify-content-center gap-2">
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
                                                                        <label class="form-label">Logo</label>
                                                                        <input type="file" class="form-control" name="logo">
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Nama Metode</label>
                                                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($edit_data['method_name'] ?? '') ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Detail Akun</label>
                                                                        <textarea class="form-control" name="details" rows="3" required><?= htmlspecialchars($edit_data['account_details'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Pilih Gambar (QRIS)</label>
                                                                        <input type="file" class="form-control" name="qris_image">
                                                                        <div class="form-text">Upload hanya jika metode ini adalah QRIS.</div>
                                                                        <?php if ($edit_mode && !empty($edit_data['qris_image_url'])): ?>
                                                                                    <img src="<?= BASE_URL ?>assets/images/qris/<?= $edit_data['qris_image_url'] ?>" class="img-fluid rounded border mt-2" style="max-height: 100px;">
                                                                        <?php endif; ?>
                                                            </div>

                                                            <div class="form-check mb-3">
                                                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($edit_data['is_active'] ?? 1) ? 'checked' : '' ?>>
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