<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            echo '<div class="alert alert-danger text-center">Anda tidak memiliki hak akses untuk melihat halaman ini.</div>';
            return;
}

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

<?= $message ?>

<div class="card content-card">
            <div class="card-header">
                        <h5 class="mb-0">Edit Konten Halaman "Tentang Kami"</h5>
            </div>
            <div class="card-body">
                        <form method="POST" action="">
                                    <div class="mb-3">
                                                <label for="about_us_content" class="form-label">Konten</label>
                                                <textarea class="form-control" name="about_us_content" id="about_us_content" rows="15"><?= htmlspecialchars($about_us_content) ?></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                                <button type="submit" name="update_about_us" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                        </form>
            </div>
</div>