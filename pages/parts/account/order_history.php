<?php
$user_id = $_SESSION['user_id'];

$limit = 10;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page - 1) * $limit;
$total_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM orders WHERE user_id = $user_id");
$total_results = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_results / $limit);
$orders_res = mysqli_query($conn, "SELECT id, created_at, total_amount, status FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
?>

<div class="card-header bg-transparent">
            <h3 class="mb-0">Riwayat Pesanan</h3>
</div>
<div class="card-body">
            <?php if (mysqli_num_rows($orders_res) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($orders_res)): ?>
                                    <div class="order-history-item">
                                                <div class="row align-items-center">
                                                            <div class="col-md-5">
                                                                        <a href="<?= BASE_URL ?>order_detail_customer?id=<?= $order['id'] ?>" class="text-decoration-none">
                                                                                    <h5 class="order-id">Pesanan #<?= $order['id'] ?></h5>
                                                                        </a>
                                                                        <p class="text-muted small mb-0">Dipesan pada: <?= date('d-m-Y | H:i', strtotime($order['created_at'])) ?></p>
                                                            </div>
                                                            <div class="col-md-3 text-md-center mt-2 mt-md-0">
                                                                        <?php
                                                                        $status = htmlspecialchars($order['status']);
                                                                        $class = '';
                                                                        switch ($order['status']) {
                                                                                    case 'Menunggu Pembayaran':
                                                                                                $class = 'text-bg-warning';
                                                                                                break;
                                                                                    case 'Menunggu Verifikasi':
                                                                                    case 'Diproses':
                                                                                    case 'Dikirim':
                                                                                                $class = 'text-bg-primary';
                                                                                                break;
                                                                                    case 'Selesai':
                                                                                                $class = 'text-bg-success';
                                                                                                break;
                                                                                    case 'Dibatalkan':
                                                                                                $class = 'text-bg-danger';
                                                                                                break;
                                                                        }

                                                                        echo '<span class="badge fs-7 rounded-pill ' . $class . '">' . $status . '</span>';
                                                                        ?>
                                                            </div>
                                                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                                                        <a href="<?= BASE_URL ?>order_detail_customer?id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm mb-1 mb-md-0"><i class="fa-solid fa-eye"></i></a>

                                                                        <?php if ($order['status'] == 'Dikirim'): ?>
                                                                                    <a href="<?= BASE_URL ?>app/order_customer_action.php?action=complete&id=<?= $order['id'] ?>" class="btn btn-success btn-sm ms-2" onclick="return confirm('Apakah Anda yakin sudah menerima pesanan ini?')">Pesanan Diterima</a>
                                                                        <?php endif; ?>
                                                            </div>
                                                </div>
                                    </div>
                        <?php endwhile; ?>
            <?php else: ?>
                        <div class="text-center p-5">
                                    <p>Anda belum memiliki riwayat pesanan.</p>
                        </div>
            <?php endif; ?>

            <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                                            <a class="page-link" href="<?= BASE_URL ?>akun?tab=riwayat_pesanan&p=<?= $page - 1 ?>">Previous</a>
                                                </li>

                                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                                        <a class="page-link" href="<?= BASE_URL ?>akun?tab=riwayat_pesanan&p=<?= $i ?>"><?= $i ?></a>
                                                            </li>
                                                <?php endfor; ?>

                                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                                            <a class="page-link" href="<?= BASE_URL ?>akun?tab=riwayat_pesanan&p=<?= $page + 1 ?>">Next</a>
                                                </li>
                                    </ul>
                        </nav>
            <?php endif; ?>
</div>