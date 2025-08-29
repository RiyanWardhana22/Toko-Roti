<?php
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<main>
            <div class="container my-5">
                        <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                                <div class="auth-card">
                                                            <div class="text-center mb-4">
                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['navbar_brand_logo']) ?>" alt="Logo" style="height: 60px;" class="mb-3">
                                                                        <h2>Buat Password Baru</h2>
                                                                        <p class="text-muted">Password baru Anda harus berbeda dari password sebelumnya.</p>
                                                            </div>

                                                            <?php if (isset($errors['generic'])): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    <?= htmlspecialchars($errors['generic']) ?>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="reset_password">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                                                        <div class="mb-3">
                                                                                    <label for="password" class="form-label">Password Baru</label>
                                                                                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                                                                                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                                    <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                                                                        </div>
                                                            </form>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>