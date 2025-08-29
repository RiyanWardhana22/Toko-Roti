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
                        <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                                <div class="auth-card">
                                                            <div class="text-center mb-4">
                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['navbar_brand_logo']) ?>" alt="Logo" style="height: 60px;" class="mb-3">
                                                                        <h2>Lupa Password</h2>
                                                                        <p class="text-muted">Masukkan email Anda yang terdaftar dan kami akan mengirimkan kode verifikasi untuk mengatur ulang password Anda.</p>
                                                            </div>

                                                            <?php if (isset($_SESSION['success_message'])): ?>
                                                                        <div class="alert alert-success" role="alert">
                                                                                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                                                                        </div>
                                                                        <?php unset($_SESSION['success_message']); ?>
                                                            <?php endif; ?>

                                                            <?php if (isset($errors['generic'])): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    <?= htmlspecialchars($errors['generic']) ?>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="request_reset">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <div class="mb-3">
                                                                                    <label for="email" class="form-label">Email Terdaftar</label>
                                                                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" required>
                                                                                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                                    <button type="submit" class="btn btn-primary btn-lg">Kirim Kode Verifikasi</button>
                                                                        </div>
                                                            </form>
                                                            <p class="mt-4 text-center">Kembali ke halaman <a href="<?= BASE_URL ?>login">Login</a></p>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>