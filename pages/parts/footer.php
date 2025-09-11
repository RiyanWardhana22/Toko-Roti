<footer class="main-footer pt-5 pb-4">
            <div class="container">
                        <div class="row">
                                    <div class="col-md-4 mb-4">
                                                <h5>Tentang <?= htmlspecialchars($site_settings['website_title'] ?? 'Toko Roti') ?></h5>
                                                <p>Nikmati banyak pilihan roti, kue berkualitas dan promo di <?php echo ($site_settings['website_title']) ?> </p>
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

                        <div class="row justify-content-center text-center my-4">
                                    <div class="col-lg-10">
                                                <div class="mb-3">
                                                            <img src="<?= BASE_URL ?>assets/images/footer/kampus_berdampak.png" alt="Kampus Merdeka" style="height: 50px; margin: 0 10px; vertical-align: middle;">
                                                            <img src="<?= BASE_URL ?>assets/images/footer/unimed.png" alt="Logo Universitas" style="height: 50px; margin: 0 10px; vertical-align: middle;">
                                                            <img src="<?= BASE_URL ?>assets/images/footer/bima.png" alt="BIMA" style="height: 50px; margin: 0 10px; vertical-align: middle;">
                                                </div>
                                                <div>
                                                            <h6 style="font-weight: bold;">Pengabdian Kepada Masyarakat - Pemberdayaan Kemitraan Masyarakat</h6>
                                                            <p class="mb-1" style="font-size: 0.9em;">
                                                                        HIBAH DPPM – Direktorat Penelitian dan Pengabdian Kepada Masyarakat Anggaran Tahun 2025 Bekerja Sama Dengan Lembaga Pengabdian Kepada Masyarakat Universitas Negeri Medan
                                                            </p>
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
<script>
            document.addEventListener('DOMContentLoaded', function() {
                        const countdownElements = document.querySelectorAll('.countdown-timer');

                        countdownElements.forEach(function(element) {
                                    const deadline = new Date(element.getAttribute('data-deadline').replace(' ', 'T')).getTime();

                                    const interval = setInterval(function() {
                                                const now = new Date().getTime();
                                                const distance = deadline - now;

                                                if (distance < 0) {
                                                            clearInterval(interval);
                                                            element.innerHTML = "WAKTU HABIS";
                                                            return;
                                                }

                                                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                                const fHours = hours < 10 ? '0' + hours : hours;
                                                const fMinutes = minutes < 10 ? '0' + minutes : minutes;
                                                const fSeconds = seconds < 10 ? '0' + seconds : seconds;

                                                element.innerHTML = `${fHours} : ${fMinutes} : ${fSeconds}`;
                                    }, 1000);
                        });
            });
</script>
</body>

</html>