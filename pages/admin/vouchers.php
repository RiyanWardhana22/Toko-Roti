<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            $code = strtoupper(mysqli_real_escape_string($conn, $_POST['code']));
            $type = $_POST['type'];
            $value = (float)$_POST['value'];
            $min_purchase = (float)$_POST['min_purchase'];
            $usage_limit = (int)$_POST['usage_limit'];
            $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($_POST['action'] == 'create') {
                        $stmt = mysqli_prepare($conn, "INSERT INTO vouchers (code, type, value, min_purchase, usage_limit, expires_at, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, "ssddisi", $code, $type, $value, $min_purchase, $usage_limit, $expires_at, $is_active);
                        if (mysqli_stmt_execute($stmt)) $message = "<div class='alert alert-success'>Voucher berhasil ditambahkan.</div>";
            } elseif ($_POST['action'] == 'update') {
                        $id = (int)$_POST['id'];
                        $stmt = mysqli_prepare($conn, "UPDATE vouchers SET code=?, type=?, value=?, min_purchase=?, usage_limit=?, expires_at=?, is_active=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, "ssddisii", $code, $type, $value, $min_purchase, $usage_limit, $expires_at, $is_active, $id);
                        if (mysqli_stmt_execute($stmt)) $message = "<div class='alert alert-success'>Voucher berhasil diperbarui.</div>";
            }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            mysqli_query($conn, "DELETE FROM vouchers WHERE id = $id");
            $message = "<div class='alert alert-success'>Voucher berhasil dihapus.</div>";
}

$edit_mode = false;
$edit_data = ['id' => '', 'code' => '', 'type' => 'percentage', 'value' => '', 'min_purchase' => 0, 'usage_limit' => 1, 'expires_at' => '', 'is_active' => 1];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $edit_mode = true;
            $id = (int)$_GET['id'];
            $res = mysqli_query($conn, "SELECT * FROM vouchers WHERE id = $id");
            $edit_data = mysqli_fetch_assoc($res);
            $edit_data['expires_at'] = !empty($edit_data['expires_at']) ? date('Y-m-d\TH:i', strtotime($edit_data['expires_at'])) : '';
}

$all_vouchers = mysqli_query($conn, "SELECT * FROM vouchers ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manajemen Voucher</h1>
</div>
<?= $message ?>
<div class="row">
            <div class="col-md-8">
                        <h3>Daftar Voucher</h3>
                        <div class="table-responsive">
                                    <table class="table table-striped">
                                                <thead>
                                                            <tr>
                                                                        <th>Kode</th>
                                                                        <th>Tipe</th>
                                                                        <th>Nilai</th>
                                                                        <th>Min. Belanja</th>
                                                                        <th>Limit/Digunakan</th>
                                                                        <th>Kedaluwarsa</th>
                                                                        <th>Status</th>
                                                                        <th>Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php while ($row = mysqli_fetch_assoc($all_vouchers)): ?>
                                                                        <tr>
                                                                                    <td><strong><?= htmlspecialchars($row['code']) ?></strong></td>
                                                                                    <td><?= ucfirst($row['type']) ?></td>
                                                                                    <td><?= $row['type'] == 'percentage' ? $row['value'] . '%' : 'Rp ' . number_format($row['value']) ?></td>
                                                                                    <td>Rp <?= number_format($row['min_purchase']) ?></td>
                                                                                    <td><?= $row['usage_count'] ?>/<?= $row['usage_limit'] ?></td>
                                                                                    <td><?= !empty($row['expires_at']) ? date('d M Y, H:i', strtotime($row['expires_at'])) : '-' ?></td>
                                                                                    <td><span class="badge <?= $row['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $row['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                                                                                    <td>
                                                                                                <a href="?page=vouchers&action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                                                                <a href="?page=vouchers&action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</a>
                                                                                    </td>
                                                                        </tr>
                                                            <?php endwhile; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>
            <div class="col-md-4">
                        <h3><?= $edit_mode ? 'Edit Voucher' : 'Tambah Voucher Baru' ?></h3>
                        <form method="POST" action="?page=vouchers">
                                    <input type="hidden" name="action" value="<?= $edit_mode ? 'update' : 'create' ?>">
                                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">

                                    <div class="mb-3">
                                                <label class="form-label">Kode Voucher</label>
                                                <input type="text" class="form-control" name="code" value="<?= htmlspecialchars($edit_data['code']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                                <label class="form-label">Tipe Diskon</label>
                                                <select name="type" class="form-select">
                                                            <option value="percentage" <?= $edit_data['type'] == 'percentage' ? 'selected' : '' ?>>Persentase (%)</option>
                                                            <option value="fixed" <?= $edit_data['type'] == 'fixed' ? 'selected' : '' ?>>Nominal Tetap (Rp)</option>
                                                </select>
                                    </div>
                                    <div class="mb-3">
                                                <label class="form-label">Nilai Diskon</label>
                                                <input type="number" step="0.01" class="form-control" name="value" value="<?= $edit_data['value'] ?>" required>
                                                <div class="form-text">Isi angka saja. Contoh: 10 untuk 10%, atau 20000 untuk Rp 20.000.</div>
                                    </div>
                                    <div class="mb-3">
                                                <label class="form-label">Minimal Pembelian (Rp)</label>
                                                <input type="number" class="form-control" name="min_purchase" value="<?= $edit_data['min_purchase'] ?>">
                                    </div>
                                    <div class="mb-3">
                                                <label class="form-label">Batas Penggunaan</label>
                                                <input type="number" class="form-control" name="usage_limit" value="<?= $edit_data['usage_limit'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                                <label class="form-label">Tanggal Kedaluwarsa (Opsional)</label>
                                                <input type="datetime-local" class="form-control" name="expires_at" value="<?= $edit_data['expires_at'] ?>">
                                    </div>
                                    <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $edit_data['is_active'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="is_active">Aktifkan voucher ini</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><?= $edit_mode ? 'Simpan Perubahan' : 'Tambah Voucher' ?></button>
                                    <?php if ($edit_mode): ?><a href="?page=vouchers" class="btn btn-secondary">Batal</a><?php endif; ?>
                        </form>
            </div>
</div>