<?php
$errors = $_SESSION['errors'] ?? [];
$old_input = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
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

                                                            <?php if (isset($errors['generic'])): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    <?= htmlspecialchars($errors['generic']) ?>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="login">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <div class="mb-3">
                                                                                    <label for="email" class="form-label">Email</label>
                                                                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" required>
                                                                                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="password" class="form-label">Password</label>
                                                                                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                                                                                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
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