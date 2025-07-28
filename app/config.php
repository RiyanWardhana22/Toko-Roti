<?php
if (session_status() == PHP_SESSION_NONE) {
            session_start();
}
date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'toko_roti');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
            die("Koneksi gagal: " . mysqli_connect_error());
}

define('BASE_URL', 'http://localhost/toko-roti/');
$auto_complete_query = "UPDATE orders SET status = 'Selesai' WHERE status = 'Dikirim' AND shipped_at IS NOT NULL AND shipped_at < NOW() - INTERVAL 1 DAY";
mysqli_query($conn, $auto_complete_query);

$auto_cancel_query = "UPDATE orders SET status = 'Dibatalkan' WHERE status = 'Menunggu Pembayaran' AND created_at < NOW() - INTERVAL 12 HOUR";
mysqli_query($conn, $auto_cancel_query);
