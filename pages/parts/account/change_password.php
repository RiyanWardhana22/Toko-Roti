<?php
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
            $old_pass = $_POST['old_password'];
            $new_pass = $_POST['new_password'];
            $confirm_pass = $_POST['confirm_password'];

            $user_res = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
            $user = mysqli_fetch_assoc($user_res);

            if (password_verify($old_pass, $user['password'])) {
                        if ($new_pass === $confirm_pass) {
                                    $new_pass_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                                    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
                                    mysqli_stmt_bind_param($stmt, "si", $new_pass_hashed, $user_id);
                                    if (mysqli_stmt_execute($stmt)) {
                                                $message = "<div class='alert alert-success'>Password berhasil diubah.</div>";
                                    }
                        } else {
                                    $message = "<div class='alert alert-danger'>Konfirmasi password baru tidak cocok.</div>";
                        }
            } else {
                        $message = "<div class='alert alert-danger'>Password lama salah.</div>";
            }
}
?>

<h3 class="card-title">Ubah Password</h3>
<form method="POST" action="<?= BASE_URL ?>akun?tab=ubah_password">
            <div class="mb-3">
                        <label for="old_password" class="form-label">Password Lama</label>
                        <input type="password" class="form-control" name="old_password" id="old_password" required>
            </div>
            <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required>
            </div>
            <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Ubah Password</button>
</form>