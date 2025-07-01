<?php
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $slide_id = (int)$_GET['id'];
            $res = mysqli_query($conn, "SELECT image_url FROM sliders WHERE id = $slide_id");
            if ($row = mysqli_fetch_assoc($res)) {
                        $image_path = '../../assets/images/sliders/' . $row['image_url'];
                        if (file_exists($image_path) && !empty($row['image_url'])) {
                                    unlink($image_path);
                        }
            }
            $stmt = mysqli_prepare($conn, "DELETE FROM sliders WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $slide_id);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>Slide berhasil dihapus.</div>";
}

$sliders = mysqli_query($conn, "SELECT * FROM sliders ORDER BY display_order ASC");
?>

<div class="row">
            <div class="col-lg-4">
                        <div class="card content-card">
                                    <div class="card-header">Tambah Slide Baru</div>
                                    <div class="card-body">
                                                <form action="<?= BASE_URL ?>app/slider_action.php" method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="action" value="create">
                                                            <div class="mb-3">
                                                                        <label class="form-label">Gambar Latar</label>
                                                                        <input type="file" class="form-control" name="image" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Teks Tombol (Opsional)</label>
                                                                        <input type="text" class="form-control" name="button_text">
                                                            </div>
                                                            <div class="mb-3">
                                                                        <label class="form-label">Link Tombol (Opsional)</label>
                                                                        <input type="text" class="form-control" name="button_link">
                                                            </div>
                                                            <div class="d-grid">
                                                                        <button type="submit" class="btn btn-primary">Tambah Slide</button>
                                                            </div>
                                                </form>
                                    </div>
                        </div>
            </div>
            <div class="col-lg-8">
                        <div class="card content-card">
                                    <div class="card-header">Daftar Slide Aktif</div>
                                    <div class="card-body">
                                                <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                        <thead>
                                                                                    <tr>
                                                                                                <th>Preview Gambar</th>
                                                                                                <th>Tombol</th>
                                                                                                <th class="text-end">Aksi</th>
                                                                                    </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (mysqli_num_rows($sliders) > 0): ?>
                                                                                                <?php while ($slide = mysqli_fetch_assoc($sliders)): ?>
                                                                                                            <tr>
                                                                                                                        <td><img src="<?= BASE_URL ?>assets/images/sliders/<?= $slide['image_url'] ?>" height="60" class="rounded"></td>
                                                                                                                        <td>
                                                                                                                                    <?php if (!empty($slide['button_text'])): ?>
                                                                                                                                                <a href="<?= htmlspecialchars($slide['button_link']) ?>" class="btn btn-outline-primary btn-sm" target="_blank"><?= htmlspecialchars($slide['button_text']) ?></a>
                                                                                                                                    <?php else: ?>
                                                                                                                                                -
                                                                                                                                    <?php endif; ?>
                                                                                                                        </td>
                                                                                                                        <td class="text-end">
                                                                                                                                    <a href="<?= BASE_URL ?>admin?page=settings_slider&action=delete&id=<?= $slide['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin?')">Hapus</a>
                                                                                                                        </td>
                                                                                                            </tr>
                                                                                                <?php endwhile; ?>
                                                                                    <?php else: ?>
                                                                                                <tr>
                                                                                                            <td colspan="3" class="text-center p-4">Belum ada slide yang ditambahkan.</td>
                                                                                                </tr>
                                                                                    <?php endif; ?>
                                                                        </tbody>
                                                            </table>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>