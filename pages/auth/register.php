<?php
require_once __DIR__ . '/../../../includes/header.php';

if (isLoggedIn()) {
            redirect('/');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi
            $errors = [];

            if (empty($name)) {
                        $errors['name'] = 'Nama lengkap harus diisi';
            }

            if (empty($email)) {
                        $errors['email'] = 'Email harus diisi';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors['email'] = 'Email tidak valid';
            } elseif (emailExists($email)) {
                        $errors['email'] = 'Email sudah terdaftar';
            }

            if (empty($phone)) {
                        $errors['phone'] = 'Nomor telepon harus diisi';
            }

            if (empty($password)) {
                        $errors['password'] = 'Password harus diisi';
            } elseif (strlen($password) < 6) {
                        $errors['password'] = 'Password minimal 6 karakter';
            }

            if ($password !== $confirm_password) {
                        $errors['confirm_password'] = 'Konfirmasi password tidak cocok';
            }

            // Jika tidak ada error, daftarkan user
            if (empty($errors)) {
                        $success = registerUser($name, $email, $phone, $password);

                        if ($success) {
                                    // Redirect ke halaman login dengan pesan sukses
                                    $_SESSION['register_success'] = true;
                                    redirect('/pages/auth/login.php');
                        } else {
                                    $errors['general'] = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
                        }
            }
}
?>

<div class="container py-5">
            <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-5">
                                    <div class="card shadow">
                                                <div class="card-body p-4">
                                                            <h2 class="text-center mb-4">Daftar Akun</h2>

                                                            <?php if (isset($errors['general'])): ?>
                                                                        <div class="alert alert-danger"><?= $errors['general'] ?></div>
                                                            <?php endif; ?>

                                                            <form method="POST">
                                                                        <div class="mb-3">
                                                                                    <label for="name" class="form-label">Nama Lengkap</label>
                                                                                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                                                                                id="name" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                                                                                    <?php if (isset($errors['name'])): ?>
                                                                                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="email" class="form-label">Email</label>
                                                                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                                                                                id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                                                                    <?php if (isset($errors['email'])): ?>
                                                                                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                                                                    <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                                                                                id="phone" name="phone" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                                                                                    <?php if (isset($errors['phone'])): ?>
                                                                                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="password" class="form-label">Password</label>
                                                                                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                                                                                id="password" name="password">
                                                                                    <?php if (isset($errors['password'])): ?>
                                                                                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                                                                    <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                                                                                id="confirm_password" name="confirm_password">
                                                                                    <?php if (isset($errors['confirm_password'])): ?>
                                                                                                <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                                                                                    <?php endif; ?>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-primary w-100 py-2">Daftar</button>
                                                            </form>

                                                            <div class="text-center mt-3">
                                                                        Sudah punya akun? <a href="/pages/auth/login.php">Login sekarang</a>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>