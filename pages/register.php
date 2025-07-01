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
                                    <div class="col-lg-6">
                                                <div class="auth-card">
                                                            <div class="text-center mb-4">
                                                                        <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($site_settings['navbar_brand_logo']) ?>" alt="Logo" style="height: 60px;" class="mb-3">
                                                                        <h2>Buat Akun Baru</h2>
                                                                        <p class="text-muted">Daftar sekarang untuk mulai berbelanja.</p>
                                                            </div>

                                                            <?php if (isset($errors['generic'])): ?>
                                                                        <div class="alert alert-danger" role="alert">
                                                                                    <?= htmlspecialchars($errors['generic']) ?>
                                                                        </div>
                                                            <?php endif; ?>

                                                            <form action="<?= BASE_URL ?>app/auth_action.php" method="POST">
                                                                        <input type="hidden" name="action" value="register">
                                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                                                        <div class="row">
                                                                                    <div class="col-md-6 mb-3">
                                                                                                <label for="name" class="form-label">Nama Lengkap</label>
                                                                                                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($old_input['name'] ?? '') ?>" required>
                                                                                                <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
                                                                                    </div>
                                                                                    <div class="col-md-6 mb-3">
                                                                                                <label for="email" class="form-label">Email</label>
                                                                                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" required>
                                                                                                <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
                                                                                    </div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="password" class="form-label">Password</label>
                                                                                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                                                                                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                                                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($old_input['phone'] ?? '') ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="address" class="form-label">Alamat</label>
                                                                                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($old_input['address'] ?? '') ?></textarea>
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