<?php
$message = '';

$settings_res = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'website_%' OR setting_key LIKE 'navbar_%'");
$settings = [];
while ($row = mysqli_fetch_assoc($settings_res)) {
            $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Settings - Informasi Website</h1>
</div>

<div class="card">
            <div class="card-header">
                        Pengaturan Dasar Website
            </div>
            <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>app/settings_action.php" enctype="multipart/form-data">
                                    <div class="mb-3">
                                                <label for="website_title" class="form-label">Judul Website</label>
                                                <input type="text" class="form-control" name="website_title" id="website_title" value="<?= htmlspecialchars($settings['website_title'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                                <label for="website_favicon" class="form-label">Ikon Website (Favicon)</label>
                                                <input class="form-control" type="file" id="website_favicon" name="website_favicon">
                                                <?php if (!empty($settings['website_favicon'])): ?>
                                                            <img src="<?= BASE_URL ?>assets/images/<?= $settings['website_favicon'] ?>" class="mt-2" style="width: 32px;">
                                                <?php endif; ?>
                                    </div>

                                    <hr>
                                    <h4>Pengaturan Navbar Brand</h4>
                                    <div class="mb-3">
                                                <label class="form-label">Tipe Brand di Navbar</label>
                                                <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="navbar_brand_type" id="type_text" value="text" <?= ($settings['navbar_brand_type'] ?? 'text') == 'text' ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="type_text">Teks</label>
                                                </div>
                                                <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="navbar_brand_type" id="type_logo" value="logo" <?= ($settings['navbar_brand_type'] ?? '') == 'logo' ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="type_logo">Logo (Gambar)</label>
                                                </div>
                                    </div>
                                    <div class="mb-3" id="field_brand_text"><label class="form-label">Teks Brand</label><input type="text" class="form-control" name="navbar_brand_text" id="navbar_brand_text" value="<?= htmlspecialchars($settings['navbar_brand_text'] ?? '') ?>"></div>
                                    <div class="mb-3" id="field_brand_logo"><label class="form-label">Upload Logo Brand</label><input class="form-control" type="file" id="navbar_brand_logo" name="navbar_brand_logo">
                                                <?php if (!empty($settings['navbar_brand_logo'])): ?>
                                                            <img src="<?= BASE_URL ?>assets/images/<?= $settings['navbar_brand_logo'] ?>" class="mt-2 bg-dark p-1" style="height: 40px;">
                                                <?php endif; ?>
                                    </div>

                                    <hr>
                                    <h4>Pengaturan Kontak Toko</h4>
                                    <div class="mb-3">
                                                <label for="contact_address" class="form-label">Alamat Toko</label>
                                                <textarea class="form-control" id="contact_address" name="contact_address" rows="3"><?= htmlspecialchars($settings['contact_address'] ?? '') ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                                <label for="contact_phone" class="form-label">Nomor Telepon</label>
                                                <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                                <label for="contact_email" class="form-label">Email Kontak</label>
                                                <input type="email" class="form-control" name="contact_email" id="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                                    </div>

                                    <hr>
                                    <h4>Pengaturan Sosial Media</h4>
                                    <div class="mb-3">
                                                <label for="social_facebook" class="form-label">Link Facebook</label>
                                                <input type="url" class="form-control" name="social_facebook" id="social_facebook" placeholder="https://facebook.com/..." value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                                <label for="social_instagram" class="form-label">Link Instagram</label>
                                                <input type="url" class="form-control" name="social_instagram" id="social_instagram" placeholder="https://instagram.com/..." value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                                <label for="social_whatsapp" class="form-label">Link WhatsApp</label>
                                                <input type="url" class="form-control" name="social_whatsapp" id="social_whatsapp" placeholder="https://wa.me/..." value="<?= htmlspecialchars($settings['social_whatsapp'] ?? '') ?>">
                                    </div>
                                    <button type="submit" name="update_website_settings" class="btn btn-primary mt-3">Simpan Semua Pengaturan</button>
                        </form>
            </div>
</div>

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