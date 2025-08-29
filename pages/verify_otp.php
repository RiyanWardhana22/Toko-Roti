<?php
if (!isset($_SESSION['reset_email'])) {
            header('Location: ' . BASE_URL . 'forgot_password');
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
                                                                        <h2>Verifikasi Kode OTP</h2>
                                                                        <p class="text-muted">Kami telah mengirimkan kode OTP 8 digit ke email <strong><?= htmlspecialchars($_SESSION['reset_email']) ?></strong>. Silakan periksa kotak masuk atau folder spam Anda.</p>
                                                            </div>

                                                            <?php if (isset($errors['generic'])): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    <?= htmlspecialchars($errors['generic']) ?>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="verify_otp">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                        <div class="mb-3">
                                                                                    <label for="otp" class="form-label">Kode OTP</label>
                                                                                    <input type="text" class="form-control <?= isset($errors['otp']) ? 'is-invalid' : '' ?>" id="otp" name="otp" required pattern="\d{8}" title="Kode OTP harus 8 digit angka.">
                                                                                    <?php if (isset($errors['otp'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['otp']) ?></div><?php endif; ?>
                                                                        </div>
                                                                        <div class="d-grid">
                                                                                    <button type="submit" class="btn btn-primary btn-lg">Verifikasi</button>
                                                                        </div>
                                                            </form>
                                                            <p class="mt-4 text-center">Tidak menerima kode? <a href="<?= BASE_URL ?>forgot_password">Kirim ulang</a></p>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>