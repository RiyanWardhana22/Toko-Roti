<footer class="main-footer pt-5 pb-4">
            <div class="container">
                        <div class="row">
                                    <div class="col-md-4 mb-4">
                                                <h5>Tentang <?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti') ?></h5>
                                                <p>Kami menyajikan roti dan kue berkualitas tinggi yang dibuat setiap hari dengan bahan-bahan pilihan dan resep warisan keluarga.</p>
                                    </div>
                                    <div class="col-md-2 mb-4">
                                                <h5>Navigasi</h5>
                                                <ul class="list-unstyled">
                                                            <li><a href="<?= BASE_URL ?>">Beranda</a></li>
                                                            <li><a href="<?= BASE_URL ?>produk">Produk</a></li>
                                                            <li><a href="<?= BASE_URL ?>about">Tentang Kami</a></li>
                                                </ul>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                                <h5>Kontak Kami</h5>
                                                <ul class="list-unstyled">
                                                            <li><i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($site_settings['contact_address'] ?? 'Alamat belum diatur') ?></li>
                                                            <li><i class="fas fa-phone me-2"></i> <?= htmlspecialchars($site_settings['contact_phone'] ?? 'Telepon belum diatur') ?></li>
                                                            <li><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($site_settings['contact_email'] ?? 'Email belum diatur') ?></li>
                                                </ul>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                                <h5>Ikuti Kami</h5>
                                                <div class="social-icons">
                                                            <?php if (!empty($site_settings['social_facebook'])): ?>
                                                                        <a href="<?= htmlspecialchars($site_settings['social_facebook']) ?>" class="text-dark" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($site_settings['social_instagram'])): ?>
                                                                        <a href="<?= htmlspecialchars($site_settings['social_instagram']) ?>" class="text-dark" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($site_settings['social_whatsapp'])): ?>
                                                                        <a href="<?= htmlspecialchars($site_settings['social_whatsapp']) ?>" class="text-dark" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
                                                            <?php endif; ?>
                                                </div>
                                    </div>
                        </div>
                        <hr>
                        <div class="text-center">
                                    <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti Anda') ?>. All Rights Reserved.</p>
                        </div>
            </div>
</footer>

<script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>