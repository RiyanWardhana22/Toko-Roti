<?php
$user_id = $_SESSION['user_id'];
$latest_order_res = mysqli_query($conn, "SELECT id, created_at, total_amount, status FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
?>

<h3 class="card-title">Dashboard</h3>
<p>Selamat Datang kembali, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</p>
<p>Dari dasbor akun Anda, Anda dapat melihat pesanan terakhir, mengelola alamat pengiriman, dan mengedit kata sandi serta detail akun Anda.</p>

<hr>
<h5>Pesanan Terakhir Anda</h5>
<?php if (mysqli_num_rows($latest_order_res) > 0): $order = mysqli_fetch_assoc($latest_order_res); ?>
            <div class="table-responsive">
                        <table class="table table-bordered">
                                    <thead>
                                                <tr>
                                                            <th>ID Pesanan</th>
                                                            <th>Tanggal</th>
                                                            <th>Total</th>
                                                            <th>Status</th>
                                                </tr>
                                    </thead>
                                    <tbody>
                                                <tr>
                                                            <td>#<?= $order['id'] ?></td>
                                                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                                            <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                            <td><span class="badge bg-primary"><?= htmlspecialchars($order['status']) ?></span></td>
                                                </tr>
                                    </tbody>
                        </table>
            </div>
<?php else: ?>
            <p>Anda belum pernah melakukan pemesanan.</p>
<?php endif; ?>