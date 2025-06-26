<?php
require_once 'app/config.php';
require_once 'app/mailer.php';

echo "<h1>Mencoba Mengirim Email Tes...</h1>";
echo "<hr>";

$penerima_email = 'riyanwardhana55@gmail.com';
$penerima_nama = 'Riyan22';

$subjek = 'Tes Pengiriman Email dari Website Toko Roti';
$isi_email = 'Halo! Jika Anda menerima email ini, berarti konfigurasi PHPMailer Anda sudah bekerja dengan benar. Selamat!';

$hasil = send_email($penerima_email, $penerima_nama, $subjek, $isi_email);

echo "<hr>";
if ($hasil === true) {
            echo "<h2>Status: Berhasil!</h2>";
            echo "<p>Email tes berhasil dikirim ke <strong>" . $penerima_email . "</strong>. Silakan cek inbox Anda.</p>";
} else {
            echo "<h2>Status: Gagal!</h2>";
            echo "<p>Terjadi error saat mengirim email. Lihat log teknis di atas untuk detailnya.</p>";
            echo "<p>Pesan Error: " . htmlspecialchars($hasil) . "</p>";
}
