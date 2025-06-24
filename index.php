<?php
// Memuat file konfigurasi utama (koneksi DB, BASE_URL, session)
require_once 'app/config.php';

// Mendapatkan dan memproses URL yang diminta pengguna
$request_uri = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url_parts = explode('/', $request_uri);
$page = $url_parts[0];

// Daftar semua halaman yang valid di website Anda
$allowed_pages = [
  'home',
  'produk',
  'cart',
  'checkout',
  'login',
  'register',
  'akun',
  'order_success',
  'admin',
  'about',
  'order_detail_customer',
  'payment_confirmation' // Disiapkan untuk langkah berikutnya
];

// Daftar halaman khusus yang tidak menggunakan template header/footer utama
// karena mereka memanggilnya sendiri di dalam file masing-masing.
$standalone_pages = ['admin', 'order_detail_customer'];


// --- Logika Routing Utama ---

// Cek dulu apakah halaman yang diminta adalah halaman 'standalone'
if (in_array($page, $standalone_pages)) {
  // Jika ya, langsung muat filenya tanpa template
  if (file_exists('pages/' . $page . '.php')) {
    include 'pages/' . $page . '.php';
  } else {
    // Jika file standalone tidak ditemukan, tetap tampilkan 404 dengan template
    include 'pages/parts/header.php';
    echo '<div class="container text-center py-5"><h1>404 - Halaman Tidak Ditemukan</h1></div>';
    include 'pages/parts/footer.php';
  }
}
// Jika bukan halaman standalone, cek apakah itu halaman biasa yang valid
elseif (in_array($page, $allowed_pages) && file_exists('pages/' . $page . '.php')) {
  // Jika ya, muat dengan template header dan footer utama
  include 'pages/parts/header.php';
  include 'pages/' . $page . '.php';
  include 'pages/parts/footer.php';
}
// Jika halaman tidak ada di daftar manapun
else {
  // Tampilkan halaman 404 Not Found
  include 'pages/parts/header.php';
  echo '<div class="container text-center py-5"><h1>404 - Halaman Tidak Ditemukan</h1></div>';
  include 'pages/parts/footer.php';
}

// Tutup koneksi database setelah semua proses selesai
if (isset($conn)) {
  mysqli_close($conn);
}
