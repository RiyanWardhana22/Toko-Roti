<?php
$user_id_to_edit = (int)$_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT id, name, email, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id_to_edit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

if (!$user_data) {
            echo "<div class='alert alert-danger'>User tidak ditemukan.</div>";
            return;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Edit Role User</h1>
            <a href="<?= BASE_URL ?>admin?page=settings_users" class="btn btn-secondary">Kembali</a>
</div>

<div class="card">
            <div class="card-body">
                        <form action="<?= BASE_URL ?>app/user_action.php" method="POST">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="user_id" value="<?= $user_data['id'] ?>">

                                    <div class="mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($user_data['name']) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <select class="form-select" name="role" id="role">
                                                            <option value="customer" <?= $user_data['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
                                                            <option value="admin" <?= $user_data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
            </div>
</div>