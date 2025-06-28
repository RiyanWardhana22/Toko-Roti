<?php
if (isset($_GET['action']) && isset($_GET['id'])) {
            $review_id = (int)$_GET['id'];
            if ($_GET['action'] == 'approve') {
                        mysqli_query($conn, "UPDATE reviews SET is_approved = 1 WHERE id = $review_id");
                        $message = "<div class='alert alert-success'>Ulasan berhasil disetujui.</div>";
            } elseif ($_GET['action'] == 'unapprove') {
                        mysqli_query($conn, "UPDATE reviews SET is_approved = 0 WHERE id = $review_id");
                        $message = "<div class='alert alert-info'>Persetujuan ulasan dibatalkan.</div>";
            } elseif ($_GET['action'] == 'delete') {
                        mysqli_query($conn, "DELETE FROM reviews WHERE id = $review_id");
                        $message = "<div class='alert alert-danger'>Ulasan berhasil dihapus.</div>";
            }
}

$reviews_res = mysqli_query($conn, "SELECT r.*, p.name as product_name, u.name as user_name FROM reviews r JOIN products p ON r.product_id = p.id JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manajemen Ulasan Pelanggan</h1>
</div>
<div class="table-responsive">
            <table class="table table-striped">
                        <thead>
                                    <tr>
                                                <th>Produk</th>
                                                <th>Pelanggan</th>
                                                <th>Rating</th>
                                                <th>Ulasan</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                    </tr>
                        </thead>
                        <tbody>
                                    <?php while ($review = mysqli_fetch_assoc($reviews_res)): ?>
                                                <tr>
                                                            <td><?= htmlspecialchars($review['product_name']) ?></td>
                                                            <td><?= htmlspecialchars($review['user_name']) ?></td>
                                                            <td>
                                                                        <?php for ($i = 1; $i <= $review['rating']; $i++): ?><i class="fas fa-star text-warning"></i><?php endfor; ?>
                                                                        <?php for ($i = $review['rating'] + 1; $i <= 5; $i++): ?><i class="fas fa-star text-secondary"></i><?php endfor; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($review['comment']) ?></td>
                                                            <td><?= date('d M Y', strtotime($review['created_at'])) ?></td>
                                                            <td>
                                                                        <span class="badge <?= $review['is_approved'] ? 'text-bg-success' : 'text-bg-warning' ?>">
                                                                                    <?= $review['is_approved'] ? 'Disetujui' : 'Menunggu' ?>
                                                                        </span>
                                                            </td>
                                                            <td>
                                                                        <?php if ($review['is_approved']): ?>
                                                                                    <a href="?page=reviews&action=unapprove&id=<?= $review['id'] ?>" class="btn btn-secondary btn-sm">Tolak</a>
                                                                        <?php else: ?>
                                                                                    <a href="?page=reviews&action=approve&id=<?= $review['id'] ?>" class="btn btn-success btn-sm">Setujui</a>
                                                                        <?php endif; ?>
                                                                        <a href="?page=reviews&action=delete&id=<?= $review['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus ulasan ini secara permanen?')">Hapus</a>
                                                            </td>
                                                </tr>
                                    <?php endwhile; ?>
                        </tbody>
            </table>
</div>