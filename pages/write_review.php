<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}
if (!isset($_GET['order_id'])) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan');
            exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

$order_check_res = mysqli_query($conn, "SELECT id FROM orders WHERE id = $order_id AND user_id = $user_id AND status = 'Selesai'");
if (mysqli_num_rows($order_check_res) == 0) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan&error=cannot_review');
            exit();
}

$items_res = mysqli_query($conn, "SELECT oi.product_id, p.name as product_name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id AND oi.has_reviewed = 0");

?>

<main>
            <div class="container py-3">
                        <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                                <div class="card account-card">
                                                            <div class="card-header bg-transparent">
                                                                        <h3 class="mb-0">Beri Ulasan untuk Pesanan #<?= $order_id ?></h3>
                                                            </div>
                                                            <div class="card-body">
                                                                        <?php if (mysqli_num_rows($items_res) > 0): ?>
                                                                                    <p>Pilih rating dan berikan ulasan Anda untuk produk yang telah Anda terima.</p>
                                                                                    <form action="<?= BASE_URL ?>app/review_action.php" method="POST">
                                                                                                <input type="hidden" name="order_id" value="<?= $order_id ?>">

                                                                                                <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                                                                                                            <div class="mb-4 border-bottom pb-4">
                                                                                                                        <div class="d-flex align-items-center mb-3">
                                                                                                                                    <img src="<?= BASE_URL ?>assets/images/<?= $item['image_url'] ?>" class="rounded me-3" width="80" alt="<?= $item['product_name'] ?>">
                                                                                                                                    <h5><?= htmlspecialchars($item['product_name']) ?></h5>
                                                                                                                        </div>
                                                                                                                        <input type="hidden" name="product_ids[]" value="<?= $item['product_id'] ?>">

                                                                                                                        <div class="mb-3">
                                                                                                                                    <label class="form-label d-block">Rating Anda:</label>
                                                                                                                                    <div class="rating">
                                                                                                                                                <input type="radio" id="star5_<?= $item['product_id'] ?>" name="rating[<?= $item['product_id'] ?>]" value="5" required /><label for="star5_<?= $item['product_id'] ?>"></label>
                                                                                                                                                <input type="radio" id="star4_<?= $item['product_id'] ?>" name="rating[<?= $item['product_id'] ?>]" value="4" /><label for="star4_<?= $item['product_id'] ?>"></label>
                                                                                                                                                <input type="radio" id="star3_<?= $item['product_id'] ?>" name="rating[<?= $item['product_id'] ?>]" value="3" /><label for="star3_<?= $item['product_id'] ?>"></label>
                                                                                                                                                <input type="radio" id="star2_<?= $item['product_id'] ?>" name="rating[<?= $item['product_id'] ?>]" value="2" /><label for="star2_<?= $item['product_id'] ?>"></label>
                                                                                                                                                <input type="radio" id="star1_<?= $item['product_id'] ?>" name="rating[<?= $item['product_id'] ?>]" value="1" /><label for="star1_<?= $item['product_id'] ?>"></label>
                                                                                                                                    </div>
                                                                                                                        </div>

                                                                                                                        <div class="mb-3">
                                                                                                                                    <label for="comment_<?= $item['product_id'] ?>" class="form-label">Ulasan Anda (Opsional):</label>
                                                                                                                                    <textarea class="form-control" name="comment[<?= $item['product_id'] ?>]" id="comment_<?= $item['product_id'] ?>" rows="3"></textarea>
                                                                                                                        </div>
                                                                                                            </div>
                                                                                                <?php endwhile; ?>

                                                                                                <div class="d-grid">
                                                                                                            <button type="submit" class="btn btn-primary btn-lg">Kirim Ulasan</button>
                                                                                                </div>
                                                                                    </form>
                                                                        <?php else: ?>
                                                                                    <div class="alert alert-info text-center">
                                                                                                <p class="mb-0">Terima kasih! Semua produk dari pesanan ini sudah Anda ulas.</p>
                                                                                                <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-secondary mt-3">Kembali</a>
                                                                                    </div>
                                                                        <?php endif; ?>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            </div>
</main>