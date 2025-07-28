<?php
$message = '';
if (isset($_GET['action']) && isset($_GET['id'])) {
            $review_id = (int)$_GET['id'];
            if ($_GET['action'] == 'approve') {
                        mysqli_query($conn, "UPDATE reviews SET is_approved = 1 WHERE id = $review_id");
                        $message = "<div class='alert alert-success'>Ulasan berhasil ditampilkan kembali.</div>";
            } elseif ($_GET['action'] == 'unapprove') {
                        mysqli_query($conn, "UPDATE reviews SET is_approved = 0 WHERE id = $review_id");
                        $message = "<div class='alert alert-info'>Ulasan berhasil disembunyikan.</div>";
            } elseif ($_GET['action'] == 'delete') {
                        mysqli_query($conn, "DELETE FROM reviews WHERE id = $review_id");
                        $message = "<div class='alert alert-danger'>Ulasan berhasil dihapus.</div>";
            }
}

$limit = 10;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;

$rating_filter = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$where_clause = '';
if ($rating_filter > 0 && $rating_filter <= 5) {
            $where_clause = "WHERE r.rating = $rating_filter";
}

$total_res = mysqli_query($conn, "SELECT COUNT(r.id) as total FROM reviews r $where_clause");
$total_results = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_results / $limit);

$reviews_res = mysqli_query($conn, "SELECT r.*, p.name as product_name, u.name as user_name FROM reviews r JOIN products p ON r.product_id = p.id JOIN users u ON r.user_id = u.id $where_clause ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset");
?>

<?= $message ?>
<div class="card content-card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <h5 class="mb-2 mb-md-0">Manajemen Ulasan</h5>
                        <div class="btn-group" role="group">
                                    <a href="?page=reviews" class="btn btn-sm <?= $rating_filter == 0 ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
                                    <?php for ($star = 5; $star >= 1; $star--): ?>
                                                <a href="?page=reviews&rating=<?= $star ?>" class="btn btn-sm <?= $rating_filter == $star ? 'btn-primary' : 'btn-outline-primary' ?>">
                                                            <i class="fas fa-star text-warning"></i> <?= $star ?>
                                                </a>
                                    <?php endfor; ?>
                        </div>
            </div>
            <div class="card-body">
                        <div class="table-responsive">
                                    <table class="table table-hover">
                                                <thead>
                                                            <tr>
                                                                        <th>Produk</th>
                                                                        <th>Pelanggan</th>
                                                                        <th class="text-center">Rating</th>
                                                                        <th>Ulasan</th>
                                                                        <th>Status</th>
                                                                        <th class="text-center">Aksi</th>
                                                            </tr>
                                                </thead>
                                                <tbody>
                                                            <?php if (mysqli_num_rows($reviews_res) > 0): ?>
                                                                        <?php while ($review = mysqli_fetch_assoc($reviews_res)): ?>
                                                                                    <tr>
                                                                                                <td><?= htmlspecialchars($review['product_name']) ?></td>
                                                                                                <td><?= htmlspecialchars($review['user_name']) ?></td>
                                                                                                <td class="text-center"><strong><?= $review['rating'] ?>/5</strong></td>
                                                                                                <td><small><?= htmlspecialchars($review['comment']) ?></small></td>
                                                                                                <td>
                                                                                                            <span class="badge <?= $review['is_approved'] ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                                                                                                        <?= $review['is_approved'] ? 'Ditampilkan' : 'Disembunyikan' ?>
                                                                                                            </span>
                                                                                                </td>
                                                                                                <td class="d-flex justify-content-center gap-2">
                                                                                                            <?php if ($review['is_approved']): ?>
                                                                                                                        <a href="?page=reviews&action=unapprove&id=<?= $review['id'] ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-eye-slash"></i></a>
                                                                                                            <?php else: ?>
                                                                                                                        <a href="?page=reviews&action=approve&id=<?= $review['id'] ?>" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-eye"></i></a>
                                                                                                            <?php endif; ?>
                                                                                                            <a href="?page=reviews&action=delete&id=<?= $review['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin?')"><i class="fa-solid fa-trash"></i></a>
                                                                                                </td>
                                                                                    </tr>
                                                                        <?php endwhile; ?>
                                                            <?php else: ?>
                                                                        <tr>
                                                                                    <td colspan="6" class="text-center p-4">Tidak ada ulasan yang cocok dengan filter ini.</td>
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
                                                                                    <a class="page-link" href="?page=reviews&rating=<?= $rating_filter ?>&p=<?= $i ?>"><?= $i ?></a>
                                                                        </li>
                                                            <?php endfor; ?>
                                                </ul>
                                    </nav>
                        <?php endif; ?>
            </div>
</div>