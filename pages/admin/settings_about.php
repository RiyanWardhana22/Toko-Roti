<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_about_us'])) {
            $new_content = $_POST['about_us_content'];
            $stmt = mysqli_prepare($conn, "INSERT INTO settings (setting_key, setting_value) VALUES ('about_us_content', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            mysqli_stmt_bind_param($stmt, "ss", $new_content, $new_content);

            if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Konten 'Tentang Kami' berhasil diperbarui.</div>";
            } else {
                        $message = "<div class='alert alert-danger'>Gagal memperbarui konten.</div>";
            }
}

$result = mysqli_query($conn, "SELECT setting_value FROM settings WHERE setting_key = 'about_us_content'");
$about_us_content = '';
if ($row = mysqli_fetch_assoc($result)) {
            $about_us_content = $row['setting_value'];
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Settings - Tentang Kami</h1>
</div>

<?= $message ?>

<div class="card">
            <div class="card-header">
                        Edit Konten Halaman "Tentang Kami"
            </div>
            <div class="card-body">
                        <form method="POST" action="">
                                    <div class="mb-3">
                                                <label for="about_us_content" class="form-label">Konten</label>
                                                <textarea class="form-control" name="about_us_content" id="about_us_content" rows="15"><?= htmlspecialchars($about_us_content) ?></textarea>
                                    </div>
                                    <button type="submit" name="update_about_us" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
            </div>
</div>