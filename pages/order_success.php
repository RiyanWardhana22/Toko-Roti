<?php
if (!isset($_GET['order_id'])) {
            header('Location: ' . BASE_URL);
            exit();
}
$order_id = (int)$_GET['order_id'];
?>
<div class="text-center py-5">
            <h1 class="text-success">Terima Kasih!</h1>
            <h2>Pesanan Anda Berhasil Dibuat.</h2>
            <p>Nomor Pesanan Anda adalah: <strong>#<?= $order_id ?></strong></p>
            <hr>
            <h4>Silahkan Konfirmasi Pembayaran</h4>
            <p class="mt-2">Setelah melakukan pembayaran, status pesanan Anda akan kami proses. Anda dapat melihat status pesanan di halaman "Akun Saya".</p>
            <a href="<?= BASE_URL ?>akun?tab=riwayat_pesanan" class="btn btn-primary mt-3">Konfirmasi Pembayaran</a>
</div>