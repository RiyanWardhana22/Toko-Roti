<?php
// Cek jika ada aksi 'view', muat halaman detail (tidak ada perubahan)
if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            include 'customer_detail.php';
            return;
}

// Logika baru untuk pencarian
$search_query = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';
$where_clause = "WHERE role = 'customer'";
if (!empty($search_query)) {
            $where_clause .= " AND (name LIKE '%$search_query%' OR email LIKE '%$search_query%')";
}

// Query utama mengambil data pelanggan dengan filter pencarian
$result = mysqli_query($conn, "SELECT id, name, email, phone, created_at FROM users $where_clause ORDER BY created_at DESC");
?>

<div class="card content-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <h5 class="mb-2 mb-md-0">Daftar Pelanggan</h5>
                        <form action="" method="GET" class="ms-md-auto">
                                    <input type="hidden" name="page" value="customers">
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
                                                                        <th>#ID</th>
                                                                        <th>Nama Pelanggan</th>
                                                                        <th>Email</th>
                                                                        <th>Telepon</th>
                                                                        <th>Tanggal Daftar</th>
                                                                        <th class="text-center">Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php
                                                            $no = 1;
                                                            if (mysqli_num_rows($result) > 0): ?>
                                                                        <?php while ($customer = mysqli_fetch_assoc($result)): ?>
                                                                                    <tr>
                                                                                                <td><?= $no++ ?></td>
                                                                                                <td><?= htmlspecialchars($customer['name']) ?></td>
                                                                                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                                                                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                                                                                <td><?= date('d M Y', strtotime($customer['created_at'])) ?></td>
                                                                                                <td class="text-end">
                                                                                                            <a href="<?= BASE_URL ?>admin?page=customers&action=view&id=<?= $customer['id'] ?>" class="btn btn-info btn-sm">Lihat Detail</a>
                                                                                                </td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else: ?>
                                                                        <tr>
                                                                                    <td colspan="6" class="text-center p-4">Tidak ada pelanggan yang cocok dengan kriteria pencarian.</td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                </tbody>
                                    </table>
                        </div>
            </div>
</div>