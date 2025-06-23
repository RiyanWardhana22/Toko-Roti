<h2>Login Pelanggan</h2>
<p>Silakan login untuk melanjutkan ke proses checkout.</p>
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
            <button type="submit" class="btn btn-primary">Login</button>
            <p class="mt-3">Belum punya akun? <a href="<?= BASE_URL ?>register">Daftar di sini</a></p>
</form>