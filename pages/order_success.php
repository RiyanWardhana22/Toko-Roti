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
            <h4>Instruksi Pembayaran</h4>
            <p>Silakan lakukan pembayaran sejumlah total pesanan Anda ke rekening berikut:</p>
            <p><strong>Bank BCA: 123-456-7890</strong> a/n Toko Roti Lezat</p>
            <p><strong>Bank Mandiri: 098-765-4321</strong> a/n Toko Roti Lezat</p>
            <p class="mt-4">Setelah melakukan pembayaran, status pesanan Anda akan kami proses. Anda dapat melihat status pesanan di halaman "Akun Saya".</p>
            <a href="<?= BASE_URL ?>produk" class="btn btn-primary mt-3">Lanjut Belanja</a>
</div>