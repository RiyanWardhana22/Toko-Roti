<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            echo '<div class="alert alert-danger text-center">Anda tidak memiliki hak akses untuk melihat halaman ini.</div>';
            return;
}

$message = '';
if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $message = "<div class='alert alert-success'>Pengaturan berhasil disimpan.</div>";
}

$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<?= $message ?>
<form method="POST" action="<?= BASE_URL ?>app/settings_action.php" enctype="multipart/form-data">
            <div class="row">
                        <div class="col-lg-8">
                                    <div class="card content-card">
                                                <div class="card-header">Pengaturan Dasar & Kontak</div>
                                                <div class="card-body">
                                                            <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                                    <label for="website_title" class="form-label">Judul Website</label>
                                                                                    <input type="text" class="form-control" name="website_title" id="website_title" value="<?= htmlspecialchars($settings['website_title'] ?? '') ?>">
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                                    <label for="contact_email" class="form-label">Email Kontak</label>
                                                                                    <input type="email" class="form-control" name="contact_email" id="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                                                                        </div>
                                                            </div>
                                                            <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                                    <label for="contact_phone" class="form-label">Nomor Telepon</label>
                                                                                    <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                                    <label for="contact_address" class="form-label">Alamat Toko</label>
                                                                                    <input type="text" class="form-control" name="contact_address" id="contact_address" value="<?= htmlspecialchars($settings['contact_address'] ?? '') ?>">
                                                                        </div>
                                                            </div>
                                                </div>
                                    </div>
                                    <div class="card content-card mt-4">
                                                <div class="card-header">Pengaturan Sosial Media</div>
                                                <div class="card-body">
                                                            <div class="mb-3"><label for="social_facebook" class="form-label">Link Facebook</label><input type="url" class="form-control" name="social_facebook" id="social_facebook" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>"></div>
                                                            <div class="mb-3"><label for="social_instagram" class="form-label">Link Instagram</label><input type="url" class="form-control" name="social_instagram" id="social_instagram" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>"></div>
                                                            <div class="mb-3"><label for="social_whatsapp" class="form-label">Link WhatsApp</label><input type="url" class="form-control" name="social_whatsapp" id="social_whatsapp" value="<?= htmlspecialchars($settings['social_whatsapp'] ?? '') ?>"></div>
                                                </div>
                                    </div>
                        </div>
                        <div class="col-lg-4">
                                    <div class="card content-card">
                                                <div class="card-header">Pengaturan Branding</div>
                                                <div class="card-body">
                                                            <div class="mb-3"><label for="website_favicon" class="form-label">Ikon Website (Favicon)</label><input class="form-control" type="file" id="website_favicon" name="website_favicon"></div>
                                                            <hr>
                                                            <h6>Brand di Navbar</h6>
                                                            <div class="form-check"><input class="form-check-input" type="radio" name="navbar_brand_type" id="type_text" value="text" <?= ($settings['navbar_brand_type'] ?? 'text') == 'text' ? 'checked' : '' ?>><label class="form-check-label" for="type_text">Teks</label></div>
                                                            <div class="form-check mb-2"><input class="form-check-input" type="radio" name="navbar_brand_type" id="type_logo" value="logo" <?= ($settings['navbar_brand_type'] ?? '') == 'logo' ? 'checked' : '' ?>><label class="form-check-label" for="type_logo">Logo</label></div>
                                                            <div class="mb-3" id="field_brand_text"><label class="form-label">Teks Brand</label><input type="text" class="form-control" name="navbar_brand_text" id="navbar_brand_text" value="<?= htmlspecialchars($settings['navbar_brand_text'] ?? '') ?>"></div>
                                                            <div class="mb-3" id="field_brand_logo"><label class="form-label">Upload Logo</label><input class="form-control" type="file" id="navbar_brand_logo" name="navbar_brand_logo"></div>
                                                </div>
                                    </div>
                                    <div class="d-grid mt-4">
                                                <button type="submit" name="update_website_settings" class="btn btn-primary">Simpan Pengaturan</button>
                                    </div>
                        </div>
            </div>
</form>

<script>
            document.addEventListener('DOMContentLoaded', function() {
                        const typeRadios = document.querySelectorAll('input[name="navbar_brand_type"]');
                        const text_field = document.getElementById('field_brand_text');
                        const logo_field = document.getElementById('field_brand_logo');

                        function toggleFields() {
                                    if (document.querySelector('input[name="navbar_brand_type"]:checked').value === 'logo') {
                                                text_field.style.display = 'none';
                                                logo_field.style.display = 'block';
                                    } else {
                                                text_field.style.display = 'block';
                                                logo_field.style.display = 'none';
                                    }
                        }

                        typeRadios.forEach(radio => radio.addEventListener('change', toggleFields));
                        toggleFields();
            });
</script>