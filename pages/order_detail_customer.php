<?php
if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit();
}

if (!isset($_GET['id'])) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan');
            exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$order_stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($order_stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
            header('Location: ' . BASE_URL . 'akun?tab=riwayat_pesanan');
            exit();
}

$items_stmt = mysqli_prepare($conn, "SELECT oi.*, p.name as product_name, p.image_url FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);

include 'parts/header.php';
?>

<div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3>Detail Pesanan #<?= htmlspecialchars($order['id']) ?></h3>
                        <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-secondary">Kembali ke Riwayat Pesanan</a>
            </div>

            <div class="card mb-4">
                        <div class="card-body">
                                    <div class="row">
                                                <div class="col-md-4">
                                                            <strong>Tanggal Pesan:</strong><br>
                                                            <?= date('d F Y, H:i', strtotime($order['created_at'])) ?>
                                                </div>
                                                <div class="col-md-4">
                                                            <strong>Total Pembayaran:</strong><br>
                                                            Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
                                                </div>
                                                <div class="col-md-4">
                                                            <strong>Status Pesanan:</strong><br>
                                                            <span class="badge bg-primary fs-6"><?= htmlspecialchars($order['status']) ?></span>
                                                </div>
                                    </div>
                        </div>
            </div>

            <div class="row g-4">
                        <div class="col-md-6">
                                    <div class="card">
                                                <div class="card-header">
                                                            <h4>Alamat Pengiriman</h4>
                                                </div>
                                                <div class="card-body">
                                                            <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                                                            <hr>
                                                            <strong>Metode Pengiriman:</strong><br>
                                                            <?= htmlspecialchars($order['shipping_method']) ?> (Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?>)
                                                </div>
                                    </div>
                        </div>
                        <div class="col-md-6">
                                    <div class="card">
                                                <div class="card-header">
                                                            <h4>Ringkasan Biaya</h4>
                                                </div>
                                                <div class="card-body">
                                                            <ul class="list-group list-group-flush">
                                                                        <li class="list-group-item d-flex justify-content-between">
                                                                                    <span>Subtotal Produk</span>
                                                                                    <span>Rp <?= number_format($order['total_amount'] - $order['shipping_cost'], 0, ',', '.') ?></span>
                                                                        </li>
                                                                        <li class="list-group-item d-flex justify-content-between">
                                                                                    <span>Ongkos Kirim</span>
                                                                                    <span>Rp <?= number_format($order['shipping_cost'], 0, ',', '.') ?></span>
                                                                        </li>
                                                                        <li class="list-group-item d-flex justify-content-between fw-bold">
                                                                                    <span>Total</span>
                                                                                    <span>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                                                                        </li>
                                                            </ul>
                                                </div>
                                    </div>
                        </div>
            </div>

            <h4 class="mt-5">Produk yang Dipesan</h4>
            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <div class="card mb-3">
                                    <div class="row g-0">
                                                <div class="col-md-2 text-center p-3">
                                                            <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded-start" alt="Gambar Produk" style="max-height: 100px;">
                                                </div>
                                                <div class="col-md-10">
                                                            <div class="card-body">
                                                                        <h5 class="card-title"><?= htmlspecialchars($item['product_name']) ?></h5>
                                                                        <p class="card-text">
                                                                                    Jumlah: <?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                                                                    <br>
                                                                                    <strong>Subtotal: Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></strong>
                                                                        </p>
                                                            </div>
                                                </div>
                                    </div>
                        </div>
            <?php endwhile; ?>
</div>

<?php
include 'parts/footer.php';
?>