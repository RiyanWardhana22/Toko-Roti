<?php
if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            include 'customer_detail.php';
            return;
}

$result = mysqli_query($conn, "SELECT id, name, email, phone, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manajemen Pelanggan</h1>
</div>

<div class="table-responsive">
            <table class="table table-striped table-sm">
                        <thead>
                                    <tr>
                                                <th scope="col">#ID</th>
                                                <th scope="col">Nama Pelanggan</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Nomor Telepon</th>
                                                <th scope="col">Tanggal Daftar</th>
                                                <th scope="col">Aksi</th>
                                    </tr>
                        </thead>
                        <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                                <?php while ($customer = mysqli_fetch_assoc($result)): ?>
                                                            <tr>
                                                                        <td><?= $customer['id'] ?></td>
                                                                        <td><?= htmlspecialchars($customer['name']) ?></td>
                                                                        <td><?= htmlspecialchars($customer['email']) ?></td>
                                                                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                                                                        <td><?= date('d M Y', strtotime($customer['created_at'])) ?></td>
                                                                        <td>
                                                                                    <a href="<?= BASE_URL ?>admin?page=customers&action=view&id=<?= $customer['id'] ?>" class="btn btn-info btn-sm">Lihat Detail</a>
                                                                        </td>
                                                            </tr>
                                                <?php endwhile; ?>
                                    <?php else: ?>
                                                <tr>
                                                            <td colspan="6" class="text-center">Belum ada pelanggan yang terdaftar.</td>
                                                </tr>
                                    <?php endif; ?>
                        </tbody>
            </table>
</div>