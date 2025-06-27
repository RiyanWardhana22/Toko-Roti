<main>
            <div class="container my-5">
                        <div class="row auth-container">
                                    <div class="col-lg-6">
                                                <div class="auth-card">
                                                            <div class="text-center mb-4">
                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['navbar_brand_logo']) ?>" alt="Logo" style="height: 60px;" class="mb-3">
                                                                        <h2>Buat Akun Baru</h2>
                                                                        <p class="text-muted">Daftar sekarang untuk mulai berbelanja.</p>
                                                            </div>

                                                            <?php if (isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    Email ini sudah terdaftar. Silakan gunakan email lain.
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="register">
                                                                        <div class="row">
                                                                                    <div class="col-md-6 mb-3">
                                                                                                <label for="name" class="form-label">Nama Lengkap</label>
                                                                                                <input type="text" class="form-control" id="name" name="name" required>
                                                                                    </div>
                                                                                    <div class="col-md-6 mb-3">
                                                                                                <label for="email" class="form-label">Email</label>
                                                                                                <input type="email" class="form-control" id="email" name="email" required>
                                                                                    </div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="password" class="form-label">Password</label>
                                                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                                                                    <input type="tel" class="form-control" id="phone" name="phone">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="address" class="form-label">Alamat</label>
                                                                                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                                    <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
                                                                        </div>
                                                            </form>
                                                            <p class="mt-4 text-center">Sudah punya akun? <a href="<?= BASE_URL ?>login">Login di sini</a></p>
                                                </div>
                                    </div>

                                    <div class="col-lg-6 d-none d-lg-block">
                                                <img src="<?= BASE_URL ?>assets/images/register.png" class="img-fluid rounded-3" alt="Register Image">
                                    </div>
                        </div>
            </div>
</main>