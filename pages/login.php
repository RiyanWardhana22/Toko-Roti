<main>
            <div class="container my-5">
                        <div class="row auth-container">
                                    <div class="col-lg-6 d-none d-lg-block">
                                                <img src="<?= BASE_URL ?>assets/images/login.png" class="img-fluid rounded-3" alt="Login Image">
                                    </div>

                                    <div class="col-lg-6">
                                                <div class="auth-card">
                                                            <div class="text-center mb-4">
                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['navbar_brand_logo']) ?>" alt="Logo" style="height: 60px;" class="mb-3">
                                                                        <h2>Selamat Datang Kembali</h2>
                                                                        <p class="text-muted">Silakan masuk untuk melanjutkan.</p>
                                                            </div>

                                                            <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials'): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    Email atau password salah.
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="login">
                                                                        <div class="mb-3">
                                                                                    <label for="email" class="form-label">Email</label>
                                                                                    <input type="email" class="form-control" id="email" name="email" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="password" class="form-label">Password</label>
                                                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                                                                        </div>
                                                            </form>
                                                            <p class="mt-4 text-center">Belum punya akun? <a href="<?= BASE_URL ?>register">Daftar di sini</a></p>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>