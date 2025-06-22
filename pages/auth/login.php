<?php
require_once __DIR__ . '/../../../includes/header.php';

if (isLoggedIn()) {
            redirect('/');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validasi
            $errors = [];

            if (empty($email)) {
                        $errors['email'] = 'Email harus diisi';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors['email'] = 'Email tidak valid';
            }

            if (empty($password)) {
                        $errors['password'] = 'Password harus diisi';
            }

            // Jika tidak ada error, coba login
            if (empty($errors)) {
                        $user = authenticateUser($email, $password);

                        if ($user) {
                                    // Set session
                                    $_SESSION['user_id'] = $user['id'];
                                    $_SESSION['name'] = $user['name'];
                                    $_SESSION['email'] = $user['email'];
                                    $_SESSION['role'] = $user['role'];

                                    // Redirect
                                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/';
                                    redirect($redirect);
                        } else {
                                    $errors['general'] = 'Email atau password salah';
                        }
            }
}
?>

<div class="container py-5">
            <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-5">
                                    <div class="card shadow">
                                                <div class="card-body p-4">
                                                            <h2 class="text-center mb-4">Login</h2>

                                                            <?php if (isset($errors['general'])): ?>
                                                                        <div class="alert alert-danger"><?= $errors['general'] ?></div>
                                                            <?php endif; ?>

                                                            <form method="POST">
                                                                        <div class="mb-3">
                                                                                    <label for="email" class="form-label">Email</label>
                                                                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                                                                                id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                                                                    <?php if (isset($errors['email'])): ?>
                                                                                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
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
                                                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                                                                    <div class="form-check">
                                                                                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                                                                                <label class="form-check-label" for="remember">Ingat saya</label>
                                                                                    </div>
                                                                                    <a href="/pages/auth/forgot-password.php">Lupa password?</a>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                                                            </form>

                                                            <div class="text-center mt-3">
                                                                        Belum punya akun? <a href="/pages/auth/register.php">Daftar sekarang</a>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>