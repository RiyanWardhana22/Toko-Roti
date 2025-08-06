<?php
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            echo '<div class="alert alert-danger text-center">Anda tidak memiliki hak akses untuk melihat halaman ini.</div>';
            return;
}

if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
            include 'customer_detail.php';
            return;
}

$limit = 10;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;

$search_query = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : '';
$where_clause = "WHERE role = 'customer'";
if (!empty($search_query)) {
            $where_clause .= " AND (name LIKE '%$search_query%' OR email LIKE '%$search_query%')";
}

$total_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM users $where_clause");
$total_results = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_results / $limit);

$result = mysqli_query($conn, "SELECT id, name, email, phone, created_at FROM users $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
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
                                                                        <th>No</th>
                                                                        <th>Nama Pelanggan</th>
                                                                        <th>Email</th>
                                                                        <th>Telepon</th>
                                                                        <th>Tanggal Daftar</th>
                                                                        <th class="text-center">Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php
                                                            $no = $offset + 1;
                                                            if (mysqli_num_rows($result) > 0): ?>
                                                                        <?php while ($customer = mysqli_fetch_assoc($result)): ?>
                                                                                    <tr>
                                                                                                <td><?= $no++ ?></td>
                                                                                                <td><?= htmlspecialchars($customer['name']) ?></td>
                                                                                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                                                                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                                                                                <td><?= date('d-m-Y', strtotime($customer['created_at'])) ?></td>
                                                                                                <td class="text-center">
                                                                                                            <a href="<?= BASE_URL ?>admin?page=customers&action=view&id=<?= $customer['id'] ?>" class="btn btn-outline-info btn-sm"><i class="fa-solid fa-magnifying-glass-plus"></i></a>
                                                                                                </td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else: ?>
                                                                        <tr>
                                                                                    <td colspan="6" class="text-center p-4">Tidak ada pelanggan yang cocok dengan kriteria.</td>
                                                                        </tr>
                                                            <?php endif; ?>
                                                </tbody>
                                    </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                                    <nav class="mt-3">
                                                <ul class="pagination justify-content-center">
                                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                                                    <a class="page-link" href="?page=customers&q=<?= urlencode($search_query) ?>&p=<?= $i ?>"><?= $i ?></a>
                                                                        </li>
                                                            <?php endfor; ?>
                                                </ul>
                                    </nav>
                        <?php endif; ?>
            </div>
</div>