<?php
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            include 'user_form.php';
            return;
}

$message = '';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $user_id_to_delete = (int)$_GET['id'];

            if ($user_id_to_delete == $_SESSION['user_id']) {
                        $message = "<div class='alert alert-danger'>Anda tidak dapat menghapus akun Anda sendiri.</div>";
            } else {
                        $order_check = mysqli_query($conn, "SELECT id FROM orders WHERE user_id = $user_id_to_delete LIMIT 1");
                        if (mysqli_num_rows($order_check) > 0) {
                                    $message = "<div class='alert alert-danger'>Gagal menghapus! User ini memiliki riwayat pesanan.</div>";
                        } else {
                                    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
                                    mysqli_stmt_bind_param($stmt, "i", $user_id_to_delete);
                                    if (mysqli_stmt_execute($stmt)) {
                                                $message = "<div class='alert alert-success'>User berhasil dihapus.</div>";
                                    }
                        }
            }
}

$search_query = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$where_clause = '';
if (!empty($search_query)) {
            $where_clause = "WHERE name LIKE '%$search_query%' OR email LIKE '%$search_query%'";
}

$users_result = mysqli_query($conn, "SELECT id, name, email, role, created_at FROM users $where_clause ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Settings - Manajemen User</h1>
</div>

<?= $message ?>
<div class="card content-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <h5 class="mb-2 mb-md-0">Manajemen Pengguna</h5>
                        <form action="" method="GET">
                                    <input type="hidden" name="page" value="settings_users">
                                    <div class="input-group">
                                                <input type="text" name="q" class="form-control" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($search_query) ?>">
                                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                        </form>
            </div>
            <div class="card-body">
                        <div class="table-responsive">
                                    <table class="table table-hover">
                                                <thead>
                                                            <tr>
                                                                        <th>NO</th>
                                                                        <th>Nama</th>
                                                                        <th>Email</th>
                                                                        <th>Role</th>
                                                                        <th>Tanggal Daftar</th>
                                                                        <th class="text-center">Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php
                                                            $no = 1;
                                                            while ($user = mysqli_fetch_assoc($users_result)):
                                                            ?>
                                                                        <tr>
                                                                                    <td><?= $no++ ?></td>
                                                                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                                                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                                                                    <td>
                                                                                                <span class="badge <?= $user['role'] == 'admin' ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                                                                                            <?= ucfirst($user['role']) ?>
                                                                                                </span>
                                                                                    </td>
                                                                                    <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                                                                    <td class="d-flex text-end gap-2">
                                                                                                <a href="<?= BASE_URL ?>admin?page=settings_users&action=edit&id=<?= $user['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-pencil"></i></a>
                                                                                                <a href="<?= BASE_URL ?>admin?page=settings_users&action=delete&id=<?= $user['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus user ini?');"><i class="fa-solid fa-trash"></i></a>
                                                                                    </td>
                                                                        </tr>
                                                            <?php endwhile; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>
</div>