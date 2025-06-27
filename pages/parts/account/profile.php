<?php
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $address = mysqli_real_escape_string($conn, $_POST['address']);

            $email_check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $user_id");
            if (mysqli_num_rows($email_check) > 0) {
                        $message = "<div class='alert alert-danger'>Email sudah digunakan oleh akun lain.</div>";
            } else {
                        $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?");
                        mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $address, $user_id);
                        if (mysqli_stmt_execute($stmt)) {
                                    $_SESSION['user_name'] = $name;
                                    $message = "<div class='alert alert-success'>Profil berhasil diperbarui.</div>";
                        }
            }
}

$user_res = mysqli_query($conn, "SELECT name, email, phone, address FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_res);
?>

<h3 class="card-title">Profil Saya</h3>
<p>Kelola informasi profil Anda untuk mempercepat proses checkout.</p>
<hr>
<form method="POST" action="<?= BASE_URL ?>akun?tab=profil">
            <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="mb-3">
                        <label for="address" class="form-label">Alamat Pengiriman Utama</label>
                        <textarea class="form-control" name="address" id="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
</form>